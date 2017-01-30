<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 6:39 PM
 */
define('BASE_PATH', __DIR__ . '/');
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    if (is_file(BASE_PATH . $class . '.php')) require_once(BASE_PATH . $class . '.php');
});