<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Exception\RouteNotFoundException;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Exception\UnimplementedError;

class Router implements Registry {
    /** @var Route[] $routes */
    protected $routes = [];
    public static function init() {
        return new static;
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
    public function resolve(Request $Request = null) {
        if (!$Request) {
            throw new UnimplementedError("Can only deal with requests");
        }
        foreach ($this->routes as $index => $route) {
            $__does_match = $route->matches($Request);
            if ($__does_match) {
                return $route->resolve($Request);
            }
        }
        throw new RouteNotFoundException("No matching routes");
    }
}