<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 9:39 PM
 */

namespace Sm\Entity;

use Sm\Abstraction\Identifier\HasObjectIdentityTrait;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\Property\NonexistentPropertyException;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\PropertyHaver;
use Sm\Error\WrongArgumentException;
use Sm\Factory\Factory;
use Sm\Query\Query;
use Sm\Storage\Container\Container;
use Sm\Util;


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
 * @property-read PropertyContainer         $Properties
 * @property-read \Sm\Entity\EntityTypeMeta $Meta
 * @property-read Factory                   $IdentifyingConditionFactory
 * @property      Property                  eg_title    example property used in testing syntax. In doc comments for autocomplete.
 * @property      Property                  eg_alias    example property used in testing syntax. In doc comments for autocomplete.
 * @property      Property                  title
 *
 * @package Sm\Entity
 */
class EntityType implements PropertyHaver {
    use HasObjectIdentityTrait;
    /** @var  EntityTypeMeta $EntityTypeMeta Information about this EntityType */
    protected $EntityTypeMeta;
    /** @var  PropertyContainer */
    protected $Properties;
    /** @var \Sm\Factory\Factory $IdentifyingConditionFactory */
    protected $IdentifyingConditionFactory;
    /** @var  \Sm\Storage\Container\Container $ExistenceCheckers A container that contains the functions that
     *                                                   should be called to check the existence
     *                                                   of this object w/r to a context.
     */
    protected $ExistenceCheckers;
    
    #-----------------------------------------------------------------------------------
    ##  Initialization/Constructors
    #-----------------------------------------------------------------------------------
    /**
     * EntityType constructor.
     *
     * @param EntityTypeMeta    $EntityTypeMeta
     * @param PropertyContainer $Properties The Entity's Property
     */
    public function __construct(EntityTypeMeta $EntityTypeMeta, PropertyContainer $Properties = null) {
        $this->_initExistenceChecker();
        $this->setObjectId(Identifier::generateIdentity($this));
        
        $this->EntityTypeMeta = $EntityTypeMeta;
        # Inherit the properties of the EntityMeta (serves as the prototype to this class)
        $this->Properties = $EntityTypeMeta->cloneProperties();
        # Also copy the properties
        if ($Properties instanceof PropertyContainer) {
            $this->Properties->inherit($Properties);
        }
        # Say that this EntityType is the owner of these Properties
        $this->Properties->addPropertyOwners($this);
    
        $this->_initIdentifyingConditionFactory();
    }
    public function __get($name) {
        if ($name === 'Properties') {
            return $this->getProperties();
        }
        if ($name === 'Meta') {
            return $this->EntityTypeMeta;
        }
        if ($name === 'IdentifyingConditionFactory') {
            return $this->IdentifyingConditionFactory;
        }
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
    
    #-----------------------------------------------------------------------------------
    ##  Getters/Setters/Checkers/Registerers
    #-----------------------------------------------------------------------------------
    public function checkExistence($context = null) {
        # Only use strings and objects
        if (!is_object($context) && !(Util::canBeString($context))) {
            throw new WrongArgumentException("Can only use strings and objects as contexts.");
        }
        
        
        $context_classname = is_string($context) ? $context : (is_object($context) ? get_class($context) : null);
        
        # Check the ExistenceCheckers to see if the classname that represents this Context exists
        return $this->ExistenceCheckers->resolve($context_classname, $this) ?: $this->ExistenceCheckers->resolve('default', $this);
    }
    /**
     * Register a function that will allow us to check whether or not something exists.
     *
     * @param string   $classname
     * @param callable $item
     *
     * @return $this
     */
    public function registerExistenceChecker($classname, callable $item) {
        $this->ExistenceCheckers->register($classname, $item);
        return $this;
    }
    /**
     * Get the Condition that Identifies this EntityType
     *
     * @param \Sm\Query\Interpreter\QueryInterpreter $context
     *
     * @param \Sm\Query\Query                        $Query
     *
     * @return Property[] An array of the properties that now have to be appended to the Query.
     */
    public function augmentQuery($context = null, Query $Query = null) {
        # it doesn't matter that this is a string because the "default" index doesn't do anything that requires a QueryInterpreter
        if (!isset($context)) {
            $context = 'default';
        }
        return $this->IdentifyingConditionFactory->build($context, $Query, $this);
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
    /**
     * Initialize the ExistenceChecker of this class.
     *
     * @return \Sm\Storage\Container\Container
     */
    private function _initExistenceChecker() {
        # The ExistenceChecker is a Container to allow us to verify that this exists based on Ancestry
        $this->ExistenceCheckers                  = new Container;
        $this->ExistenceCheckers->search_ancestry = true;
        # Register a default Existence Checker to deny the Existence of this Entity
        $this->ExistenceCheckers->register('default', function () { return false; });
        return $this->ExistenceCheckers;
    }
    /**
     * Initialize the IdentifyingConditionFactory of this class
     *
     * @return \Sm\Factory\Factory
     */
    private function _initIdentifyingConditionFactory() {
        $this->IdentifyingConditionFactory = new Factory;
        $this->IdentifyingConditionFactory->doNotCreateMissing();
        $this->IdentifyingConditionFactory->register(function () { return false; }, 'default');
        return $this->IdentifyingConditionFactory;
    }
}