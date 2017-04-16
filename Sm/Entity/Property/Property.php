<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:30 PM
 */

namespace Sm\Entity\Property;


use Sm\Abstraction\ReadonlyTrait;
use Sm\Entity\EntityTypeVariable;
use Sm\Storage\Source\NullSource;
use Sm\Storage\Source\Source;
use Sm\Storage\Source\SourceHaver;
use Sm\Type\Variable_\Variable_;

/**
 * Class Property
 *
 * Represents a property held by an Entity or Model
 *
 * @mixin ReadonlyTrait
 * @mixin Variable_
 *
 * @package Sm\Entity\Property
 * @property-read string $name
 * @property-read string $object_id
 */
class Property extends Variable_ implements SourceHaver {
    
    use ReadonlyTrait;
    
    /** @var  string $name */
    protected $name;
    /** @var  Source $Source */
    protected $Source;
    /**
     * @var \Sm\Entity\Property\PropertyHaver[] $PropertyHavers The objects that hold this property
     */
    protected $PropertyHavers = [];
    /**
     * Property constructor.
     *
     * @param mixed|null  $name
     * @param Source|null $Source
     */
    public function __construct($name = null, Source $Source = null) {
        if (isset($name)) {
            $this->name = $name;
        }
        if (isset($Source)) {
            $this->Source = $Source;
        }
        
        parent::__construct(null);
    }
    public function __get($name) {
        if ($name === 'object_id') {
            return $this->_object_id;
        }
        # todo (bug or feature?) returns $this when "value" would resolves to null bc of variable resolution thing
        return parent::__get($name);
    }
    /**
     * Setter for this Property
     *
     * @param $name
     * @param $value
     *
     * @throws \Sm\Entity\Property\ReadonlyPropertyException
     */
    public function __set($name, $value) {
        if ($this->isReadonly()) {
            throw new ReadonlyPropertyException("Cannot modify a readonly property");
        }
        parent::__set($name, $value);
    }
    /**
     * Sometimes multiple objects hold references to the same Property.
     * This allows the property to know which objects hold it.
     *
     * @param \Sm\Entity\Property\PropertyHaver $PropertyHaver
     *
     * @return $this
     */
    public function addPropertyHaver(PropertyHaver $PropertyHaver = null) {
        # If this PropertyContainer was previously owned by an EntityTypeVariable
        if (($this->PropertyHavers[0] ?? false) instanceof EntityTypeVariable) {
            return $this->setPropertyHaver($PropertyHaver);
        }
        if (!in_array($PropertyHaver, $this->PropertyHavers)) {
            $this->PropertyHavers[] = $PropertyHaver;
        }
        return $this;
    }
    /**
     * Remove an PropertyHaver from this Property
     *
     * @param \Sm\Entity\Property\PropertyHaver $PropertyHaver
     *
     * @return $this
     */
    public function removePropertyHaver(PropertyHaver $PropertyHaver) {
        $index = array_search($PropertyHaver, $this->PropertyHavers);
        if ($index !== false) {
            unset($this->PropertyHavers[ $index ]);
        }
        return $this;
    }
    /**
     * Make it so the only PropertyHaver of this Property
     *
     * @param \Sm\Entity\Property\PropertyHaver|null $PropertyHaver
     *
     * @return $this
     */
    public function setPropertyHaver(PropertyHaver $PropertyHaver = null) {
        $this->PropertyHavers = isset($PropertyHaver) ? [ $PropertyHaver ] : [];
        return $this;
    }
    /**
     * Get the array of objects that hold this Property
     *
     * @return \Sm\Entity\Property\PropertyHaver[]
     */
    public function getPropertyHavers() {
        return $this->PropertyHavers;
    }
    /**
     * Set the name of the property
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }
    
    
    /**
     * Get the Source of the class
     *
     * @return Source
     */
    public function getSource(): Source {
        return $this->Source = $this->Source ?? NullSource::init();
    }
    /**
     * @param Source $Source
     *
     * @return Property
     */
    public function setSource(Source $Source) {
        $this->Source = $Source;
        return $this;
    }
}