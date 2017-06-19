<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Request\Request;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Application\App;
use Sm\Core\Error\UnimplementedError;
use Sm\Core\Resolvable\Error\UnresolvableError;

class Router implements Registry {
    /** @var Route[] $routes */
    protected $routes = [];
    /** @var \Sm\Core\Application\App $App */
    protected $App;
    
    /**
     * Router constructor.
     *
     * @param \Sm\Core\Application\App|null $App
     */
    public function __construct(App $App = null) {
        if (isset($App)) {
            $this->App = $App;
        }
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function register($name, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register($index, $item);
            }
            return $this;
        } else if (!($registrand instanceof Route)) {
            $registrand = Route::coerce($registrand);
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
            $Request = Request::coerce($Request);
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