<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 6:33 PM
 */

namespace Sm\Abstraction\Identifier;


/**
 * Class HasObjectIdentityTrait
 *
 * Used for objects that implement the Identifiable interface.
 *
 * @see     \Sm\Abstraction\Identifier\Identifiable
 * @package Sm\Abstraction\Identifier
 */
trait HasObjectIdentityTrait {
    protected $_object_id;
    
    /**
     * Get the ID that uniquely identifies this object.
     *
     * @return string
     */
    public function getObjectId() {
        return $this->_object_id;
    }
    /**
     * Set the object ID. Only permit this to happen once.
     *
     * @param $object_id
     *
     * @return $this
     */
    public function setObjectId($object_id) {
        $this->_object_id = $this->_object_id ?? $object_id;
        return $this;
    }
    public function __toString() {
        return "{$this->_object_id}";
    }
}