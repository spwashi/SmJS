<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\Application\App;
use Sm\Communication\Request\Request;
use Sm\Core\Resolvable\OnceRunResolvable;
use Sm\Data\Query\Query;

return [
    'init' => function (App $App) {
        # Set a way to receive the request, typical factories
        $App->registerDefaults([ 'Request' =>
                                     OnceRunResolvable::init(function ($url = null) {
                                         return \Sm\Communication\Request\Request::init($url??Request::getRequestUrl());
                                     }) ]);
        
        # Set the default controller namespace - used in routing
        $App->registerDefaults('controller_namespace',
                               OnceRunResolvable::init(function ($App) {
                                   return '\\' . ($App->name??'Sm') . '\\Controller\\';
                               }),
                               true);
        
        
        # Load the autoload script
        $autoload_file = $App->Paths->to_config('autoload.php', true);
        if ($autoload_file) {
            require_once $autoload_file;
        }
        
        # Load the Routing module
        $routing_module = SM_PATH . 'Communication/Routing/routing.sm.module.php';
        if (is_file($routing_module)) $App->Modules->routing = include $routing_module ?? [];
    
    
        $sql_module = SM_PATH . 'Storage/Modules/Sql/MySql/mysql.sql.sm.module.php';
        if (is_file($sql_module)) $App->Modules->sql = include $sql_module ?? [];
        
        
        $App->register('Query', function () use ($App) {
            return Query::init()->setFactoryContainer($App->Factories);
        });
        
        return $App;
    },
];