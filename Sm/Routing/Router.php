<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Routing;


use Sm\Abstraction\Registry;
use Sm\App\App;
use Sm\Resolvable\Error\UnresolvableError;

class Router implements Registry {
    /** @var Route[] $routes */
    protected $routes = [ ];
    public function __construct(App $App = null) {
        if (isset($this->app))
            $this->app = $App;
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function register($identifier, $registrand = null) {
        if (is_array($identifier)) {
            foreach ($identifier as $index => $item) {
                $this->register($index, $item);
            }
            return $this;
        } else if (!($registrand instanceof Route)) {
            $registrand = Route::coerce($registrand);
        }
        if (is_string($identifier))
            $this->routes[ $identifier ] = $registrand;
        else
            $this->routes[] = $registrand;
        return $this;
    }
    public function resolve($identifier = null) {
        if (class_exists('\Sm\Request\Request') && !($identifier instanceof \Sm\Abstraction\Request\Request)) {
            $identifier = \Sm\Request\Request::coerce($identifier);
        }
        foreach ($this->routes as $index => $route) {
            $__does_match = $route->matches($identifier);
            if ($__does_match) return $route->resolve($identifier);
        }
        throw new UnresolvableError("No matching routes");
    }
    public static function init(App $App = null) {
        return new static($App);
    }
}