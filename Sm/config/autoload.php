<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 1:11 AM
 */
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $path  = BASE_PATH . "{$class}.php";
    if (is_file($path)) require_once($path);
});