<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:48 PM
 */

namespace Sm\Storage\Source;


use Sm\Authentication\Authentication;

/**
 * Class Source
 *
 * Represents something that can be queried
 *
 * @package Sm\Storage\Source
 */
abstract class Source {
    /** @var  Authentication $Authentication Represents the Authenticated connection to whatever source */
    protected $Authentication;
    abstract public function isAuthenticated();
    public function authenticate(Authentication $Authentication = null) {
        $this->Authentication = $Authentication;
        return $this;
    }
    abstract public function getName();
    /**
     * Get the root Source of this Source. Useful for subsources
     *
     * @return \Sm\Storage\Source\Source
     */
    public function getRootSource(): Source {
        return $this;
    }
    /**
     * Static constructor
     *
     * @return static
     */
    public static function init() {
        return new static(...func_get_args());
    }
}