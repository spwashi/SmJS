<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\SingletonFunctionResolvable;
use Sm\View\Template\TemplateFactory;
use Sm\View\ViewFactory;

return [
    'init' =>
    /**
     * @param \Sm\App\App $App
     */
        function (App $App) {
            $App->register_defaults(
                'Request',
                SingletonFunctionResolvable::coerce(function ($url = null) {
                    return \Sm\Request\Request::coerce($url??Request::getRequestUrl());
                })
            );
    
            $App->register_defaults(
                'template.factory',
                SingletonFunctionResolvable::coerce(function () {
                    return new TemplateFactory;
                })
            );
    
            $App->register_defaults(
                'view.factory',
                SingletonFunctionResolvable::coerce(function () {
                    return new ViewFactory;
                })
            );
    
            $autoload_file = $App->Paths->to_config('autoload.php', true);
            if ($autoload_file) require_once $autoload_file;
    
    
            $routing_module = $App->Paths->to_base('Sm/Routing/routing.sm.module.php');
            if (is_file($routing_module)) $App->Modules->routing = include $routing_module ?? [ ];
            
            
            return $App;
        },
];