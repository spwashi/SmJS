<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 9:39 PM
 */

namespace Sm\Entity;

use Sm\Entity\Property\NonexistentPropertyException;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\PropertyHaver;


/**
 * Class EntityType
 *
 * @todo    rename
 *
 * Used to represent one type or subtype of an entity.
 * E.g.) User, Page
 *
 * This is what allows an entity to be represented by two different entity types
 *
 * @property-read PropertyContainer $Properties
 * @property-read Property          eg_title    example property used in testing syntax. In doc comments for autocomplete.
 * @property-read Property          eg_alias    example property used in testing syntax. In doc comments for autocomplete.
 * @property      Property          title
 *
 * @package Sm\Entity
 */
class EntityType implements PropertyHaver {
    /** @var  EntityTypeMeta $EntityTypeMeta Information about this EntityType */
    protected $EntityTypeMeta;
    /** @var  PropertyContainer */
    protected $Properties;
    /**
     * EntityType constructor.
     *
     * @param EntityTypeMeta    $EntityTypeMeta
     * @param PropertyContainer $Properties The Entity's Property
     */
    public function __construct(EntityTypeMeta $EntityTypeMeta, PropertyContainer $Properties = null) {
        $this->EntityTypeMeta = $EntityTypeMeta;
        
        # Inherit the properties of the EntityMeta (serves as the prototype to this class)
        $this->Properties = $EntityTypeMeta->cloneProperties();
        
        # Also copy the properties
        if ($Properties instanceof PropertyContainer)
            $this->Properties->inherit($Properties);
        
        # Say that this EntityType is the owner of these Properties
        $this->Properties->setOwner($this);
    }
    
    public function __get($name) {
        if ($name === 'Properties') return $this->getProperties();
        /** @var Property $Property */
        if ($Property = $this->Properties->resolve($name)) {
            return $Property->markReadonly();
        }
        return null;
    }
    public function __set($name, $value) {
        /** @var Property $Property */
        
        if ($Property = $this->Properties->resolve($name)) {
            $Property->markNotReadonly();
            $Property->value = $value;
        } else {
            throw new NonexistentPropertyException("The requested Property '{$name}' does not exist for this EntityType");
        }
    }
    
    
    /**
     * Get the Properties of the EntityType
     *
     * @return PropertyContainer
     */
    public function getProperties(): PropertyContainer {
        # i'm toasty right now tbh, so this "readonly" stuff might be bullshit
        #   basically, I'm assuming that we never want to mutate the PropertyContainer outside of the context of
        #   this EntityType (fair?)
        #   So, we should return a readonly version of the property container
        return $this->Properties->markReadonly();
    }
}