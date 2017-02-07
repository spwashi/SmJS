<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 11:47 PM
 */
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $path  = BASE_PATH . "SmTest/{$class}.php";
    if (is_file($path)) require_once($path);
});