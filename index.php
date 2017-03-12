<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 12:46 AM
 */
use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Output\Output;

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
define('SYSTEM_LOG_PATH', __DIR__ . '/logs/');
define('APP_MODULE', BASE_PATH . 'Sm/App/app.sm.module.php');
define('SM_PATH', BASE_PATH . 'Sm/');
require_once BASE_PATH . 'Sm/config/autoload.php';

/** @var App $App */
$App                = App::init();
$App->Modules->_app = include APP_MODULE ??[];

/** @var Module $RoutingModule */
$RoutingModule = $App->Modules->routing;
if (!$App->Modules->routing) die("Malformed site configuration!");


# route the app to a controller method/function, retrieve that response
$response = $App->Modules->routing($App->Request);

# todo make an output module
Output::out($response);