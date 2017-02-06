<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 6:39 PM
 */
define('BASE_PATH', __DIR__ . '/');
define('APP_MODULE', BASE_PATH . '/Sm/App/app.sm.module.php');
spl_autoload_register(function ($class) {
    require_once BASE_PATH . 'Sm.php';
    $class = str_replace('\\', '/', $class);
    if (is_file(BASE_PATH . $class . '.php')) require_once(BASE_PATH . $class . '.php');
});