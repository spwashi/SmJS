<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


use Sm\Application\App;
use Sm\Communication\Request\Request;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Error\UnimplementedError;
use Sm\Core\Resolvable\Error\UnresolvableError;

class Router implements Registry {
    /** @var Route[] $routes */
    protected $routes = [];
    /** @var \Sm\Application\App $App */
    protected $App;
    
    /**
     * Router constructor.
     *
     * @param \Sm\Application\App|null $App
     */
    public function __construct(App $App = null) {
        if (isset($App)) {
            $this->App = $App;
        }
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function getRoutes() {
        return $this->routes;
    }
    public function register($name, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register($index, $item);
            }
            return $this;
        } else if (!($registrand instanceof Route)) {
            $resolution = $pattern = null;
            
            if (is_array($registrand)) {
                if (count($registrand) === 1) {
                    $pattern    = key($registrand);
                    $resolution = $registrand[ $pattern ];
                } else {
                    $resolution = $registrand;
                }
            }
            $registrand = Route::init($resolution, $pattern);
        }
        if (is_string($name)) {
            $this->routes[ $name ] = $registrand;
        } else {
            $this->routes[] = $registrand;
        }
        return $this;
    }
    public function resolve($Request = null) {
        if (class_exists('\Sm\Communication\Request\Request')) {
            $Request = Request::init($Request);
        } else {
            throw new UnimplementedError("Can only deal with requests");
        }
        foreach ($this->routes as $index => $route) {
            $__does_match = $route->matches($Request);
            if ($__does_match) {
                return $route->resolve($Request);
            }
        }
        $msg = "No matching routes";
        
        if ($this->App) {
            $msg .= " in {$this->App->name}";
        }
        
        throw new UnresolvableError($msg);
    }
    public static function init(App $App = null) {
        return new static($App);
    }
}