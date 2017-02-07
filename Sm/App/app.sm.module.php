<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 3:18 PM
 */

use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\ResolvableFactory;
use Sm\Resolvable\SingletonFunctionResolvable;
use Sm\View\Template\TemplateFactory;
use Sm\View\ViewFactory;

return [
    'init' => function (App $App) {
        # Set a way to receive the request, typical factories
        $App->register_defaults([ 'Request' =>
                                      SingletonFunctionResolvable::coerce(function ($url = null) {
                                          return \Sm\Request\Request::coerce($url??Request::getRequestUrl());
                                      }),
        
                                  'template.factory' =>
                                      SingletonFunctionResolvable::coerce(function () { return new TemplateFactory; }),
        
                                  'view.factory' =>
                                      SingletonFunctionResolvable::coerce(function () { return new ViewFactory; }),
        
                                  'resolvable.factory' =>
                                      SingletonFunctionResolvable::coerce(function () { return new ResolvableFactory; }), ]);
        
        # Set the default controller namespace - used in routing
        $App->register_defaults('controller_namespace',
                                SingletonFunctionResolvable::coerce(function ($App) {
                                    return '\\' . ($App->name??'Sm') . '\\Controller\\';
                                }),
                                true);
        
        
        # Load the autoload script
        $autoload_file = $App->Paths->to_config('autoload.php', true);
        if ($autoload_file) require_once $autoload_file;
        
        # Load the Routing module
        $routing_module = $App->Paths->to_base('Sm/Routing/routing.sm.module.php');
        if (is_file($routing_module)) $App->Modules->routing = include $routing_module ?? [ ];
        
        
        return $App;
    },
];