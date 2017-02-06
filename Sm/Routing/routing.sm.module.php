<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 1:28 PM
 */

use Sm\Abstraction\Registry;
use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Routing\Router;

return [
    'init'     => function (App $App) {
    
        # Default path to routes configuration is relative to the config path
        $config_path = "{$App->Paths->config_path}routes.sm.config.php";
    
    
        # Get an array representative of the configuration
        $config_arr = file_exists($config_path) ? include $config_path : [ ];
    
    
        # Define a router to use, register routes
        $App->Router = Router::init($App)
                             ->register($config_arr);
    },

    'dispatch' => function (App $App, Request $Request = null) {
    
        # Standardize the request
        $Request = $App->Request = Request::coerce($Request)->setApp($App);
    
        /** @var Router $Router */
        $Router = $App->Router;
    
        # All we require is that the router is a registry
        if (!$Router instanceof Registry) throw new UnresolvableError("Invalid Router");
    
        # This should return an "output" from the Route that was matched
        return $Router->resolve($Request);
    },
];