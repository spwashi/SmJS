<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:11 PM
 */

namespace Sm\Entity\Property;


use Sm\Abstraction\ReadonlyTrait;
use Sm\Entity\Source\DataSource;
use Sm\Entity\Source\NullDataSource;
use Sm\Error\Error;
use Sm\Error\WrongArgumentException;
use Sm\Storage\Container\Container;

/**
 * Class PropertyContainer
 *
 * Container for Properties, held by Models and Entities
 *
 * @package Sm\Entity\Property
 * @method Property current()
 * @property \Sm\Entity\Property\Property $id
 */
class PropertyContainer extends Container {
    use ReadonlyTrait;
    
    /** @var  PropertyHaver $PropertyHaver Whatever these properties belong to */
    protected $PropertyHaver;
    /** @var  \Sm\Entity\Source\DataSource $Source If there is a source that all of these Properties should belong to */
    protected $Source;
    
    /**
     * Rules for cloning the PropertyContainer
     */
    public function __clone() {
        foreach ($this->registry as $key => &$item) {
            $this->registry[ $key ] = (clone $item);
        }
        $this->addPropertyPropertyHavers(null);
    }
    /**
     * @param null|string $name
     *
     * @return \Sm\Entity\Property\Property
     */
    public function resolve($name = null) {
        return $this->getItem($name);
    }
    /**
     * Add a Property to this class, naming it if it is not already named.
     *
     * @param \Sm\Entity\Property\Property|string $name
     * @param \Sm\Entity\Property\Property        $registrand
     *
     * @return $this
     * @throws \Sm\Entity\Property\ReadonlyPropertyException If we try to add a property to
     *                                                       this PropertyContainer while the
     *                                                       readonly flag is set
     *
     * @throws \Sm\Error\WrongArgumentException If we try to register anything
     *                                          that isn't a named Property or array of named properties
     */
    public function register($name = null, $registrand = null) {
        # Don't register readonly PropertyContainers
        if ($this->readonly) {
            throw new ReadonlyPropertyException("Trying to add a property to a readonly PropertyContainer.");
        }
        
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register(!is_numeric($index) ? $index : null, $item);
            }
            return $this;
        } else {
            # If the first parameter is a property
            if ($name instanceof Property && isset($name->name)) {
                return $this->register($name->name, $name);
            }
            # We can only register Properties
            if (!($registrand instanceof Property)) {
                throw new WrongArgumentException("Can only add Properties to the PropertyContainer");
            }
            
            # We can only register named Properties
            if (!isset($name)) {
                throw new WrongArgumentException("Must name properties.");
            }
            
            # Set the name of the property based on this one
            if (!isset($registrand->name)) {
                $registrand->setName($name);
            }
        }
        /** @var static $result */
        $result = parent::register($name, $registrand);
        $this->addPropertyHaverToProperty($registrand);
        if ($registrand->getSource() instanceof NullDataSource && isset($this->Source)) {
            $registrand->setSource($this->Source);
        }
        return $result;
    }
    /**
     * Remove an element from this property container.
     * Return that element
     *
     * @param string $name The name of the variable that we want to remove
     *
     * @return mixed The variable that we removed
     *
     * @throws \Sm\Entity\Property\ReadonlyPropertyException If we try to remove a property while this class has been marked as readonly
     */
    public function remove($name) {
        if ($this->readonly) {
            throw new ReadonlyPropertyException("Cannot remove elements from a readonly PropertyContainer.");
        }
        return parent::remove($name);
    }
    /**
     * Get the PropertyHaver of these Properties
     *
     * @return PropertyHaver|null
     */
    public function getPropertyHaver() {
        return $this->PropertyHaver;
    }
    /**
     * Set the PropertyHaver of this PropertyContainer.
     * Allows us to have a reference to whatever holds these Properties.
     *
     * @todo This is probably a bad idea...
     *
     * @param PropertyHaver $PropertyHaver
     *
     * @return $this
     */
    public function setPropertyHaver(PropertyHaver $PropertyHaver = null) {
        $this->PropertyHaver = $PropertyHaver;
        return $this;
    }
    /**
     * Sets the PropertyHaver for all of the Properties as well as the PropertyContainer
     *
     * @param \Sm\Entity\Property\PropertyHaver|null $PropertyHaver
     *
     * @return $this
     */
    public function addPropertyPropertyHavers(PropertyHaver $PropertyHaver = null) {
        $this->setPropertyHaver($PropertyHaver);
        # Add the PropertyHaver to each property
        foreach ($this as $name => $Property) {
            if (isset($PropertyHaver)) {
                $this->addPropertyHaverToProperty($name);
            } else {
                # remove the PropertyHaver from the Property
                $Property->setPropertyHaver(null);
            }
        }
        return $this;
    }
    /**
     * Get all of the PropertyHavers held by the Properties in a PropertyContainer
     *
     * @return array
     */
    public function getPropertyPropertyHavers(): array {
        $PropertyHavers = [];
        foreach ($this as $index => $property) {
            $PropertyHavers = array_merge($PropertyHavers, $property->getPropertyHavers());
        }
        return array_unique($PropertyHavers);
    }
    /**
     * @return \Sm\Entity\Source\DataSource
     */
    public function getSource() {
        return $this->Source;
    }
    /**
     * @param \Sm\Entity\Source\DataSource $Source
     *
     * @return PropertyContainer
     */
    public function setSource(DataSource $Source) {
        $this->Source = $Source;
        return $this;
    }
    /**
     * Add an PropertyHaver to the Property with the following name
     *
     * @param $name
     *
     * @return $this
     * @throws \Sm\Error\Error
     */
    protected function addPropertyHaverToProperty($name) {
        if (isset($this->PropertyHaver)) {
            $Property = $name instanceof Property ? $name : $this->resolve($name);
    
            if ($Property) {
                $Property->addPropertyHaver($this->PropertyHaver);
            } else {
                throw new Error("Cannot find property {$name}");
            }
        }
        return $this;
    }
}