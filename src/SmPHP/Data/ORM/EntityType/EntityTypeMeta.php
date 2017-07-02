<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 5:19 PM
 */

namespace Sm\Data\ORM\EntityType;

use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Data\Property\PropertyContainer;

/**
 * Class EntityTypeMeta
 * Represents the information we need to know about a particular type of Entity
 *
 *
 * @package Sm\Data\ORM\EntityType
 */
class EntityTypeMeta implements Identifiable {
    use HasObjectIdentityTrait;
    /** @var  PropertyContainer $Properties Container for Prototypic properties */
    public $Properties;
    /** @var  string $name The name of the EntityType */
    public $name;
    
    protected function __construct() {
        $this->createSelfID();
        $this->Properties = (new PropertyContainer)->addPropertyPropertyHavers(EntityTypeVariable::init());
    }
    /**
     * Static constructor of the EntityType
     *
     * @return static
     */
    public static function init() {
        return new static;
    }
    public function __clone() {
        $this->Properties = $this->cloneProperties();
    }
    /**
     * Clone the Properties of the EntityType
     *
     * @return \Sm\Data\Property\PropertyContainer
     */
    public function cloneProperties() {
        $Properties    = clone $this->Properties;
        $PropertyHaver = EntityTypeVariable::init();
        $Properties->addPropertyPropertyHavers($PropertyHaver);
        foreach ($Properties as $property) {
//            echo $property->getObjectId() . "\t owned by\t" . $PropertyHaver->getObjectId() . "\n";
        }
        return $Properties;
    }
    /**
     * Get the Properties of the EntityType
     *
     * @return \Sm\Data\Property\PropertyContainer
     */
    public function getProperties(): PropertyContainer {
        return $this->Properties;
    }
    /**
     * Set the Properties of the class
     *
     * @param \Sm\Data\Property\PropertyContainer $PropertyContainer
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
        return $this->_name;
    }
    /**
     * @param string $name
     *
     * @return EntityTypeMeta
     */
    public function setName(string $name): EntityTypeMeta {
        $this->_name = $name;
        return $this;
    }
}