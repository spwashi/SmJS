<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 6:39 PM
 */
define('BASE_PATH', __DIR__ . '/');
define('SYSTEM_LOG_PATH', __DIR__ . '/logs/');
define('APP_MODULE', BASE_PATH . '/Sm/App/app.sm.module.php');
spl_autoload_register(function ($class_string) {
    $class = explode('\\', $class_string);
    
    if (isset($class[0]) && isset($class[1]) && $class[1] === 'Test') {
        $class[0] = array_shift($class) . $class[0];
    } else if (end($class) && strpos($class[ key($class) ], 'Test') > 0) {
        $class[0] = $class[0] . 'Test';
    }
    
    $class = implode('/', $class);
    $path  = BASE_PATH . "{$class}.php";
    if (is_file($path)) require_once($path);
});