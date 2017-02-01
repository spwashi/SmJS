<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 1:11 AM
 */
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    if (is_file(BASE_PATH . $class . '.php')) require_once(BASE_PATH . $class . '.php');
});