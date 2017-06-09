<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:48 PM
 */

namespace Sm\Entity\Source;


use Sm\Abstraction\Identifier\HasObjectIdentityTrait;
use Sm\Abstraction\Identifier\Identifiable;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Authentication\Authentication;

/**
 * Class Source
 *
 * Represents something that can be queried
 *
 * @package Sm\Entity\Source
 */
abstract class DataSource implements Identifiable {
    use HasObjectIdentityTrait;
    /** @var  Authentication $Authentication Represents the Authenticated connection to whatever source */
    protected $Authentication;
    /**
     * Source constructor.
     *
     * @param Authentication $Authentication
     */
    public function __construct(Authentication $Authentication = null) {
        if (isset($Authentication)) {
            $this->Authentication = $Authentication;
        }
        $this->setObjectId(Identifier::generateIdentity($this));
    }
    
    abstract public function isAuthenticated();
    public function authenticate(Authentication $Authentication = null) {
        $this->Authentication = $Authentication;
        return $this;
    }
    abstract public function getName();
    /**
     * Get the root Source of this Source. Useful for subsources
     *
     * @return \Sm\Entity\Source\DataSource
     */
    public function getRootSource(): DataSource {
        return $this;
    }
    /**
     * Static constructor
     *
     * @param null $Authentication
     *
     * @return static
     */
    public static function init($Authentication = null) {
        return new static(...func_get_args());
    }
}