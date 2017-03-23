<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 5:19 PM
 */

namespace Sm\Entity;

use Sm\Abstraction\Identifier\HasObjectIdentityTrait;
use Sm\Abstraction\Identifier\Identifiable;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\Property\PropertyContainer;

/**
 * Class EntityTypeMeta
 * Represents the information we need to know about a particular type of Entity
 *
 *
 * @package Sm\Entity
 */
class EntityTypeMeta implements Identifiable {
    use HasObjectIdentityTrait;
    /** @var  PropertyContainer $Properties Container for Prototypic properties */
    public $Properties;
    /** @var  string $name The name of the EntityType */
    public $name;
    
    protected function __construct() {
        $this->setObjectId(Identifier::generateIdentity($this));
        $this->Properties = (new PropertyContainer)->setOwner(EntityVariable::init());
    }
    public function __clone() {
        $this->Properties = $this->cloneProperties();
    }
    /**
     * Clone the Properties of the EntityType
     *
     * @return \Sm\Entity\Property\PropertyContainer
     */
    public function cloneProperties() {
        $Properties = clone $this->Properties;
        $Properties->setOwner(EntityVariable::init());
        return $Properties;
    }
    /**
     * Get the Properties of the EntityType
     *
     * @return \Sm\Entity\Property\PropertyContainer
     */
    public function getProperties(): PropertyContainer {
        return $this->Properties;
    }
    /**
     * Set the Properties of the class
     *
     * @param \Sm\Entity\Property\PropertyContainer $PropertyContainer
     *
     * @return $this
     */
    public function setProperties(PropertyContainer $PropertyContainer) {
        $this->Properties = $PropertyContainer;
        return $this;
    }
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    /**
     * @param string $name
     *
     * @return EntityTypeMeta
     */
    public function setName(string $name): EntityTypeMeta {
        $this->name = $name;
        return $this;
    }
    /**
     * Static constructor of the EntityType
     *
     * @return static
     */
    public static function init() {
        return new static;
    }
}