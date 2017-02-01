<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 1:28 PM
 */

use Sm\Abstraction\Registry;
use Sm\Abstraction\Resolvable\Arguments;
use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Routing\Router;

return [
    'init'     => function (App $App) {
        $config_path = $App->Paths->config_path . 'routes.sm.config.php';
        $config_arr  = file_exists($config_path) ? include $config_path : [ ];
        $App->register('router', Router::init($App)->register($config_arr));
    },
    'dispatch' => function (App $App, Arguments $Arguments) {
        /** @var Request $Request */
        $Request = $Arguments->getParameter('Request') ?? $Arguments->getArgument(0);
        $Request = $App->resolve('request', $Request);
        $Request->setApp($App);
        $Router = $App->resolve('router');
        if (!$Router instanceof Registry) throw new UnresolvableError("Invalid Router");
        return $Router->resolve($Request);
    },
];