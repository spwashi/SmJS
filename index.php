<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 12:46 AM
 */
use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Request\Request;

//<editor-fold desc="TESTING PURPOSES ONLY">
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);
ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
error_reporting(-1);
//*/
//</editor-fold>

ob_start();
define('BASE_PATH', __DIR__ . '/');
require_once BASE_PATH . 'Sm/config/autoload.php';

$App                   = App::init();
$App->name             = 'Sm';
$App->Paths->base_path = BASE_PATH;
$app_module_path       = $App->Paths->base_path . 'Sm/App/app.sm.module.php';
$AppModule             = Module::init(include $app_module_path ??[ ], $App);
$App                   = $App->register('app.module', $AppModule);

/** @var Module $RoutingModule */
$RoutingModule = $App->resolve('routing.module');
if (!$RoutingModule) die("Malformed site configuration!");

echo $RoutingModule->dispatch($App, Request::getRequestUrl());