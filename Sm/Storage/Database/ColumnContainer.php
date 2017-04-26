<?php
/**
 * User: Sam Washington
 * Date: 4/16/17
 * Time: 8:37 PM
 */

namespace Sm\Storage\Database;


use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Error\WrongArgumentException;
use Sm\Util;

/** @noinspection PhpHierarchyChecksInspection */

/**
 * Class ColumnContainer
 *
 * @package      Sm\Storage\Database
 *
 * @property-write array $primary_keys
 *
 * @method Property current()
 *
 */
class ColumnContainer extends PropertyContainer {
    protected $_primary_keys = [];
    
    
    public function __set($name, $value) {
        if ($name === 'primary_keys') {
            $value = is_array($value) ? $value : [ $value ];
            return $this->setPrimaryKeys(...$value);
        }
        return parent::__set($name, $value);
    }
    /**
     * Check to see if we've described a Property as being the Primary key in this ColumnContainer
     *
     * @param $property
     *
     * @return bool
     * @throws \Sm\Error\WrongArgumentException
     */
    public function isPrimarykey($property) {
        if (is_string($property)) {
            $property_name = $property;
        } else if ($property instanceof Property) {
            $property_name = $property->name;
        } else {
            $_property_type = Util::getShapeOfItem($property);
            throw new WrongArgumentException("Cannot tell if {$_property_type} is primary or not");
        }
        return in_array($property_name, $this->_primary_keys);
    }
    public function setPrimaryKeys(...$_primary_keys) {
        $this->_primary_keys = [];
        foreach ($_primary_keys as $property) {
            if ($property instanceof Property) {
                $property = $property->getName();
            } else if (!is_string($property)) {
                $type = Util::getShapeOfItem($property);
                throw new WrongArgumentException("Cannot set '{$type}' as primary key.");
            }
            
            $this->_primary_keys[] = $property;
        }
        return $this;
    }
}