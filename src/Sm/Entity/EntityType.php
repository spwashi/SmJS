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
use Sm\Factory\Factory;
use Sm\Query\Query;
use Sm\Query\QueryAugmentor;
use Sm\Query\WhereClause;


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
class EntityType implements PropertyHaver, QueryAugmentor {
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
    #  Initialization/Constructors
    #-----------------------------------------------------------------------------------
    /**
     * EntityType constructor.
     *
     * @param EntityTypeMeta    $EntityTypeMeta
     * @param PropertyContainer $Properties The Entity's Property
     */
    public function __construct(EntityTypeMeta $EntityTypeMeta, PropertyContainer $Properties = null) {
        $this->setObjectId(Identifier::generateIdentity($this));
        
        $this->EntityTypeMeta = $EntityTypeMeta;
        # Inherit the properties of the EntityMeta (serves as the prototype to this class)
        $this->Properties = $EntityTypeMeta->cloneProperties();
        # Also copy the properties
        if ($Properties instanceof PropertyContainer) {
            $this->Properties->inherit($Properties);
        }
        # Say that this EntityType is the PropertyHaver of these Properties
        $this->Properties->addPropertyPropertyHavers($this);
        
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
            $Property->markReadonly();
            return $Property;
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
    public function augmentQuery(Query $Query): Query {
        $do_add_where = in_array($Query->getQueryType(), [
            Query::QUERY_TYPE_SELECT,
            Query::QUERY_TYPE_UPDATE,
            Query::QUERY_TYPE_DELETE,
        ]);
        
        if ($do_add_where) {
            $Query->select($this->Properties->id)->where(WhereClause::equals_($this->Properties->id,
                                                                              $this->Properties->id->value));
        }
        return $Query;
    }
    #-----------------------------------------------------------------------------------
    #  Getters/Setters/Checkers/Registerers
    #-----------------------------------------------------------------------------------
    
    #-----------------------------------------------------------------------------------
    #  Getters/Setters/Checkers/Registerers
    #-----------------------------------------------------------------------------------
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
        $this->Properties->markReadonly();
        return $this->Properties;
    }
}