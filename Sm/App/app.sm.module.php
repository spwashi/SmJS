<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Request\Request;
use Sm\Resolvable\SingletonFunctionResolvable;

return [
    'init'     => /**
     * @param \Sm\App\App $App
     */
        function (App $App) {
            $App->Paths->register_defaults('base_path',
                                           BASE_PATH);
            
            $App->Paths->register_defaults('app_path',
                                           SingletonFunctionResolvable::coerce(function ($Paths, $App) {
                                               return $Paths->base_path . ($App->name??'Sm');
                                           }));
            
            $App->Paths->register_defaults('config_path',
                                           SingletonFunctionResolvable::coerce(function ($Paths) {
                                               return $Paths->app_path . 'config/default/';
                                           }));
    
            $App->register_defaults('request', SingletonFunctionResolvable::coerce(function ($url = null) {
                return \Sm\Request\Request::coerce($url??Request::getRequestUrl());
            }));
            
            $autoload_file = $App->Paths->config_path . 'autoload.php';
            if (is_file($autoload_file)) require_once $autoload_file;
            
            
            $routing_module = BASE_PATH . 'Sm/Routing/routing.sm.module.php';
            if (is_file($routing_module)) {
                $Routing = Module::init(include $routing_module ?? [ ])->setApp($App)->initialize();
                $App->register('routing.module', $Routing);
            }
            return $App;
        },
    'dispatch' => function (App $App) {
        return $App;
    },
];