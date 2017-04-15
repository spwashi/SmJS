<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\App\App;
use Sm\Query\Query;
use Sm\Request\Request;
use Sm\Resolvable\SingletonFunctionResolvable;

return [
    'init' => function (App $App) {
        # Set a way to receive the request, typical factories
        $App->registerDefaults([ 'Request' =>
                                     SingletonFunctionResolvable::coerce(function ($url = null) {
                                         return \Sm\Request\Request::coerce($url??Request::getRequestUrl());
                                     }) ]);
        
        # Set the default controller namespace - used in routing
        $App->registerDefaults('controller_namespace',
                               SingletonFunctionResolvable::coerce(function ($App) {
                                   return '\\' . ($App->name??'Sm') . '\\Controller\\';
                               }),
                               true);
        
        
        # Load the autoload script
        $autoload_file = $App->Paths->to_config('autoload.php', true);
        if ($autoload_file) {
            require_once $autoload_file;
        }
        
        # Load the Routing module
        $routing_module = $App->Paths->to_base('Sm/Routing/routing.sm.module.php');
        if (is_file($routing_module)) {
            $App->Modules->routing = include $routing_module ?? [];
        }
        
        $sql_module = $App->Paths->to_base('Sm/Storage/Modules/Sql/MySql/mysql.sql.sm.module.php');
        if (is_file($sql_module)) {
            $App->Modules->sql = include $sql_module ?? [];
        }
        
        $App->register('Query', function () use ($App) {
            return Query::init()->setFactoryContainer($App->Factories);
        });
        
        return $App;
    },
];