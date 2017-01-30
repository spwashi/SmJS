<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:26 PM
 */

namespace Sm\Routing;


use Sm\Abstraction\Registry;
use Sm\App\App;

class Router implements Registry {
    public function __construct(App $App = null) {
        if (isset($this->app))
            $this->app = $App;
    }
    public static function init(App $App = null) {
        return new static($App);
    }
    public function register($identifier, $registrand=null) {
        // TODO: Implement register() method.
    }
    public function resolve($identifier) {
        // TODO: Implement resolve() method.
    }
}