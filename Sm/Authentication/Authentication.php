<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:20 PM
 */

namespace Sm\Authentication;

/**
 * Class Authentication
 *
 * A class meant to represent a connection to a resource
 *
 * @package Sm\Authentication
 */
abstract class Authentication {
    protected $connection;
    /**
     * Is the authentication still valid?
     *
     * @return bool
     */
    abstract public function isValid(): bool;
    /**
     * Connect to the Authentication using the available credentials
     *
     * @return  bool
     */
    abstract public function connect();
    /**
     * Set the credentials
     *
     * @return static
     */
    abstract public function setCredentials();
    /**
     * Static constructor
     *
     * @return static
     */
    public static function init() {
        return new static(...func_get_args());
    }
}