<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:52 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Abstraction\Identifier\Identifiable;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\EntityType;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\PropertyHaver;
use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Query\Query;
use Sm\Storage\Container\Mini\MiniContainer;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceAsAliasFragment;
use Sm\Storage\Modules\Sql\Interpreter\QuerySubInterpreter;
use Sm\Storage\Modules\Sql\SqlModule;

/**
 * Class MysqlQuerySubInterpreter
 *
 * Meant to provide a common interface for executing Mysql Queries of a given type
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
abstract class MysqlQuerySubInterpreter extends QuerySubInterpreter {
    protected $PropertyArray;
    
    public function execute() {
        $Fragment = $this->createStatement();
        echo "{$Fragment}\n\n--------------------------\n\n";
        return;
    }
    public function createStatement() {
        $Fragment = $this->createFragment();
        $stmt     = $this->completeStatementFormatting($this->SqlModule->format($Fragment));
        return $stmt;
    }
    public function createSourceFragments() {
        $this->initSourceMap(false);
        
        $SourceArray          = $this->Source_object_id__Owner_object_id_array__map;
        $SourceFragment_array = [];
        foreach ($SourceArray as $_Source_object_id => $_Owner_object_id_array) {
            $_Source = Identifier::identify($_Source_object_id);
            if (!$_Source) {
                continue;
            }
            foreach ($_Owner_object_id_array as $_Owner_object_id => $count) {
                $SourceFragment_array [] = SourceAsAliasFragment::init()
                                                                ->setSource($_Source)
                                                                ->setOwnerObjectId($_Owner_object_id);
            }
        }
        return $SourceFragment_array;
    }
    public function createPropertyFragments() {
        $PropertyArray          = $this->initPropertyArray(true)->PropertyArray;
        $PropertyFragment_array = [];
        /** @var Property $Property */
        foreach ($PropertyArray as $Property) {
            $Owners = $Property->getOwners();
            if (count($Owners) > 1) {
                throw new UnimplementedError("Cannot yet create PropertyFragments for Properties owned by multiple objects");
            }
            
            $object_id = $Owners[0]->getObjectId();
            
            
            $SourceFragment = SourceAsAliasFragment::init()
                                                   ->setOwnerObjectId($object_id)
                                                   ->setSource($Property->getSource());
            $PropertyFragment_array[]
                            = PropertyFragment::init()
                                              ->setProperty($Property)
                                              ->setSourceFragment($SourceFragment);
        }
        return $PropertyFragment_array;
    }
    /**
     * @return Property[]|PropertyHaver[]
     */
    abstract public function getQueryProperties();
    /**
     * Make sure the Statement is in its final form
     *
     * @param $SqlStatement
     *
     * @return mixed
     */
    public function completeStatementFormatting($SqlStatement) {
        # Everything that was put in there as a placeholder should come out
        $this->replaceAliasedStringsInStatement($SqlStatement);
        $this->replaceAliasedStringsInStatement($SqlStatement);
        
        return $SqlStatement;
    }
    #
    ##
    #
    public static function create(Query $Query, SqlModule $SqlModule) {
        $Instance            = new static;
        $Instance->Query     = $Query;
        $Instance->SqlModule = $SqlModule;
        return $Instance;
    }
    /**
     * Initialize an array that maps the Object ID of a Source to an array of numbers indexed by the object ID of an Owner
     *
     * @param bool $redo
     *
     * @return $this
     */
    protected function initSourceMap($redo = false) {
        # If we've already done this and we aren't sure that we want to redo the process
        if (isset($this->Source_object_id__Owner_object_id_array__map) && !$redo) {
            return $this;
        }
        
        # Make sure we have all of the Owners
        $Owner_object_id__Properties_map = $this->initOwnerArray()->Owner_object_id__Properties_map;
        $PropertyArray                   = $this->initPropertyArray(true)->PropertyArray;
        
        # This is the array that contains a list of owners that use a given Source
        $this->Source_object_id__Owner_object_id_array__map = new MiniContainer;
        /** @var \Sm\Storage\Container\Mini\MiniContainer $src_ownr_map */
        $src_ownr_map = &$this->Source_object_id__Owner_object_id_array__map;
        
        
        foreach ($Owner_object_id__Properties_map as $_owner_object_id => $_Owner_PropertyArray) {
            /**
             * @var Property $_Property
             */
            foreach ($PropertyArray as $_Property_object_id => $_Property) {
                # This is an array, indexed by source id, of the object_ids of Owners that use this Source
                $_Source    = $_Property->getSource();
                $_source_id = $_Source->getObjectId();
                
                $src_ownr_map->registerDefault($_source_id, new MiniContainer);
                
                # We register the Object ID of the Owner to be the Count of the items in the SourceOwnerMap that have
                # this same Source ID so we can know whether we need to alias it or not
                # If the Count is greater than 0, we alias it
                $src_ownr_map->{$_source_id}->registerDefault($_owner_object_id,
                                                              $src_ownr_map->{$_source_id}->count());
                # Keep track of the owner IDs
                # The "count" lets us know if there is more than one item under this source ID
            }
        }
        
        return $this;
    }
    /**
     * If this Query relies on Properties (most do), return an array of those Properties
     *
     * @param bool $redo If we've already done this, should we do it again?
     *
     * @return $this
     */
    protected function initPropertyArray($redo = false) {
        if (isset($this->PropertyArray) && !$redo) {
            return $this;
        }
        $PropertyArray       = $this->getQueryProperties();
        $this->PropertyArray = static::flattenPropertyArray($PropertyArray);
        return $this;
    }
    /**
     * Initialize an array of the properties held by each different Owner
     *
     * @param bool $redo If the owner array has already been initialized, should we re-run this function?
     *
     * @return $this
     */
    protected function initOwnerArray($redo = false) {
        if (isset($this->Owner_object_id__Properties_map) && !$redo) {
            return $this;
        }
        $PropertyArray = $this->initPropertyArray()->PropertyArray;
        /**
         * @var array $Owner_object_id__Properties_map An array mapping Source ID to the objects that are trying to use them.
         *                     If multiple classes use one Source (probably a TableSource), we probably need to alias them.
         */
        $Owner_object_id__Properties_map = [];
        foreach ($PropertyArray as $index => $item) {
            # Use $item as an array
            if ($item instanceof Property) {
                $item = [ $item ];
            } else if ($item instanceof PropertyHaver && $item instanceof Identifiable) {
                $Owner_object_id__Properties_map[ $item->getObjectId() ] = $Owner_object_id__Properties_map[ $item->getObjectId() ] ?? [];
                continue;
            } else if (!($item instanceof PropertyContainer)) {
                continue;
            }
            
            # Append the source to an array
            foreach ($item as $property) {
                static::addPropertyToOwnerArray($property, $Owner_object_id__Properties_map);
            }
        }
        
        foreach (array_keys($Owner_object_id__Properties_map) as $owner_id) {
            $this->augmentQueryForOwner(Identifier::identify($owner_id));
        }
        $this->Owner_object_id__Properties_map = $Owner_object_id__Properties_map;
        
        # Sometimes augmenting the Query adds new Properties. This just makes sure they have been added to the Property Array
        return $this->initPropertyArray(true);
    }
    /**
     * For one owner of a property, modify this query based on how they say it needs to be modified.
     * A common thing that a query might be augmented for is adding an extra condition to the "Where" clause
     *
     * @param                 $Owner
     *
     * @return $this
     */
    protected function augmentQueryForOwner($Owner) {
        if ($Owner instanceof EntityType) {
            $Owner->augmentQuery($this, $this->Query);
        }
        return $this;
    }
    /**
     * For everything that we've aliased, replace the aliased strings with their final values
     *
     * @param $SqlStatement
     */
    protected function replaceAliasedStringsInStatement(&$SqlStatement) {
        foreach ($this->SqlModule->FormatterFactory->Aliases as $alias => $item) {
            $SqlStatement = str_replace($alias, $this->SqlModule->format($item), $SqlStatement);
        }
    }
    #
    ##
    #
    /**
     * Add this property to the array of (array of properties indexed by Property object_id) indexed by Owner object_id
     *
     * @param Property $Property    The property that we are dealing with. Assumed to have a TableSource as a source.
     * @param array    $OwnerArray  A reference to the array that we want to add this Property's information to.
     *                              Indexed by owner, contains an array of properties indexed by their
     *                              object id
     *
     * @throws \Sm\Error\Error
     * @throws \Sm\Error\UnimplementedError For now, we can only use Properties that only have one Owner
     */
    private static function addPropertyToOwnerArray(Property $Property, &$OwnerArray) {
        /** @var Identifiable[] $owners */
        $owners = $Property->getOwners();
        
        if (count($owners) > 1) {
            throw new UnimplementedError("Functionality required to interact with properties that have more than one Owner is not yet implemented.");
        } else if (count($owners) < 1) {
            throw new Error("The property must be owned. $Property");
        }
        
        # An array, indexed by the object_id of the Source
        $OwnerArray = $OwnerArray ?? [];
        
        # Append the owners to the array
        foreach ($owners as $owner) {
            $OwnerArray[ $owner->getObjectId() ]                             = $OwnerArray[ $owner->getObjectId() ] ??[];
            $OwnerArray[ $owner->getObjectId() ][ $Property->getObjectId() ] = $Property;
        }
    }
    /**
     * Given an array that contains either Properties or PropertyContainers, get all of the Properties mentioned
     *
     * @param $PropertyArray
     *
     * @return array
     */
    private static function flattenPropertyArray($PropertyArray) {
        $Properties = [];
        # Iterate through the properties and PropertyContainers to get the
        foreach ($PropertyArray as $PropertyOrContainer) {
            if ($PropertyOrContainer instanceof Property) {
                $Properties[] = $PropertyOrContainer;
            } else if ($PropertyOrContainer instanceof PropertyContainer) {
                foreach ($PropertyOrContainer as $property) {
                    $Properties[] = $property;
                }
            }
        }
        return $Properties;
    }
}