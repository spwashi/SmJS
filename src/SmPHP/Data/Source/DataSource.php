<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:48 PM
 */

namespace Sm\Data\Source;


use Sm\Authentication\Authentication;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;

/**
 * Class DataSource
 *
 * Represents something that can be queried
 *
 */
abstract class DataSource implements Identifiable {
    use HasObjectIdentityTrait;
    /** @var  Authentication $authentication Represents the Authenticated connection to whatever source */
    protected $authentication;
    /**
     * DataSource constructor.
     *
     * @param Authentication $Authentication
     */
    public function __construct(Authentication $Authentication = null) {
        if (isset($Authentication)) {
            $this->authentication = $Authentication;
        }
        $this->createSelfID();
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
    /**
     * Get the root DataSource of this DataSource. Useful for subsources
     *
     * @return \Sm\Data\Source\DataSource
     */
    public function getRootSource(): DataSource {
        return $this;
    }
    abstract public function isAuthenticated();
    
    ####################################################
    #   Action methods
    ####################################################
    abstract public function getName();
    protected function authenticate(Authentication $Authentication = null) {
        $this->authentication = $Authentication;
        return $this;
    }
}