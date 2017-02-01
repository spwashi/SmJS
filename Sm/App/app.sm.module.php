<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Resolvable\SingletonFunctionResolvable;

return [
    'init' => /**
     * @param \Sm\App\App $App
     */
        function (App $App) {
            $App->Paths->register_defaults('base_path',
                                           BASE_PATH);
            
            $App->Paths->register_defaults('app_path',
                                           $App->name ? $App->name : 'Sm');
            
            $App->Paths->register_defaults('config_path',
                                           SingletonFunctionResolvable::coerce(function ($Paths) {
                                               return $Paths->app_path . 'config/default/';
                                           }));
            
            $autoload_file = $App->Paths->config_path . 'autoload.php';
            if (is_file($autoload_file)) require_once $autoload_file;
            
            
            $routing_module = BASE_PATH . 'Sm/Routing/routing.sm.module.php';
            if (is_file($routing_module)) {
                $Routing = Module::init(include $routing_module ?? [ ], $App);
                $App->register('routing.module', $Routing);
            }
        },
];