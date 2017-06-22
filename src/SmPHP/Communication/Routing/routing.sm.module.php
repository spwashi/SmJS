<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 1:28 PM
 */

use Sm\Application\App;
use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Router;
use Sm\Core\Abstraction\Registry;
use Sm\Core\Resolvable\Error\UnresolvableError;

return [
    'init' => function (App $App) {
    
        # Default path to routes configuration is relative to the _config path
        $config_path = $App->Paths->to_config("default/routes.sm.config.php", true)
            ?: $App->Paths->to_config("routes.sm.config.php", true);
        if (!$config_path) {
            throw new UnresolvableError("There are no routes configured for this Application ($App->name) using " . $App->Paths->to_config("default/routes.sm.config.php"));
        }
        
        # Get an array representative of the configuration
        $config_arr = file_exists($config_path) ? include $config_path : [];
        
        
        # Define a router to use, register routes
        $App->Router = Router::init()
                             ->register($config_arr);
    },
    
    'dispatch' => function (App $App, Request $Request = null) {
        # Assure that the Application has been booted
        $App->Modules->_app->dispatch();
        
        # Standardize the request
        $Request = $App->Request = Request::init($Request);
        
        /** @var Router $Router */
        $Router = $App->Router;
        
        # All we require is that the router is a registry
        if (!$Router instanceof Registry) {
            throw new UnresolvableError("Invalid Router");
        }
        
        # This should return an "output" from the Route that was matched
        return $Router->resolve($Request);
    },
];