<?php
/**
 * User: Sam Washington
 * Date: 4/26/17
 * Time: 6:02 PM
 */
spl_autoload_register(function ($class_string) {
    $class = explode('\\', $class_string);
    
    $path = SRC_PATH;
    
    if (isset($class[0]) && isset($class[1]) && $class[1] === 'Test') {
        $class[0] = array_shift($class) . $class[0];
    } else if (end($class) && strpos($class[ key($class) ], 'Test') > 0) {
        $class[0] = $class[0] . 'Test';
        $path     = TEST_PATH;
    }
    
    $class = implode('/', $class);
    $path  .= "{$class}.php";
    if (is_file($path)) require_once($path);
});
