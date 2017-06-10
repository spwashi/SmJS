<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 6:39 PM
 */

define('BASE_PATH', realpath(__DIR__ . '/../../') . '/');
#
define('TEST_PATH', BASE_PATH . 'tests/');
define('EXAMPLE_APP_PATH', TEST_PATH . 'ExampleApp/');
#

define('SRC_PATH', BASE_PATH . 'src/');
define('FRAMEWORK_NAME', 'SmPHP');
define('FRAMEWORK_FROM_SRC', FRAMEWORK_NAME . '/');
define('SM_PATH', SRC_PATH . FRAMEWORK_FROM_SRC);
define('SYSTEM_LOG_PATH', BASE_PATH . 'logs/');
define('APP_MODULE', SM_PATH . 'App/app.sm.module.php');

require_once SM_PATH . 'config/autoload.php';
require_once TEST_PATH . 'config/autoload.php';