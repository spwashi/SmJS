<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:04 PM
 */

namespace Sm\Abstraction;


interface Registry {
    public function register($identifier, $registrand = null);
    public function resolve();
    public function __get($name);
}