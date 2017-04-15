<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:24 PM
 */

namespace Sm\Authentication;

/**
 * Class PasswordAuthentication
 *
 * Represents something authenticated by a password.
 *
 * @package Sm\Authentication
 */
abstract class PasswordAuthentication extends Authentication {
    private $username;
    private $password;
    
    /**
     * Set the credentials
     *
     * @param null $username
     * @param null $password
     *
     * @return static
     */
    public function setCredentials($username = null, $password = null) {
        if (isset($username)) {
            $this->username = $username;
        }
        if (isset($password)) {
            $this->password = $password;
        }
        return $this;
    }
    protected final function getPassword() { return $this->password; }
    protected final function getUsername() { return $this->username; }
}