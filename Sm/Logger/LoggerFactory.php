<?php
/**
 * User: Sam Washington
 * Date: 3/1/17
 * Time: 9:30 PM
 */

namespace Sm\Logger;


use Sm\Factory\Factory;

/**
 * Class LoggerFactory.
 *
 * Works with the "Monolog" class to build Logger instances
 *
 * @package Sm\Logger
 */
class LoggerFactory extends Factory {
    /**
     * @param string $name
     * @param int    $severity
     *
     * @return \Monolog\Logger
     */
    public function build($name = null, $severity = null) {
        return parent::build(...func_get_args());
    }
}