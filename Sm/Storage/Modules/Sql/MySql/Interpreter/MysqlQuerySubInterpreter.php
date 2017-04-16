<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:52 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Abstraction\Identifier\Identifiable;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\PropertyHaver;
use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Query\Query;
use Sm\Storage\Container\Mini\MiniContainer;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceAsAliasFragment;
use Sm\Storage\Modules\Sql\Interpreter\QuerySubInterpreter;
use Sm\Storage\Modules\Sql\SqlModule;
use Sm\Util;

/**
 * Class MysqlQuerySubInterpreter
 *
 * Meant to provide a common interface for executing Mysql Queries of a given type
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
abstract class MysqlQuerySubInterpreter extends QuerySubInterpreter {
    protected $PropertyArray;
    /** @var bool Should we alias any of the sources? Mainly for select, update, and delete queries */
    protected $do_alias_sources = true;
    
    public function execute() {
        $Fragment = $this->createStatement();
        echo "{$Fragment}\n\n--------------------------\n\n";
        return;
    }
    /**
     * Get the Properties that this Query Subinterpreter will use
     *
     * @return \Sm\Entity\Property\Property[]|\Sm\Entity\Property\PropertyHaver[]
     * @throws \Sm\Error\Error
     */
    public function getQueryProperties() {
        $query_type = $this->Query->getQueryType();
        
        $properties = $this->Query->{$query_type};
        if (!is_array($properties)) throw new Error("There was an error getting the Properties used by {$query_type}");
        return $properties;
    }
    public function createStatement() {
        $Fragment = $this->createFragment();
        $stmt     = $this->completeStatementFormatting($this->SqlModule->format($Fragment));
        return $stmt;
    }
    
    #
    ##
    #
    /**
     * Make an array for the Fragments of Sources used in this Query
     *
     * @return \Sm\Storage\Modules\Sql\Formatter\SourceFragment[]
     */
    public function createSourceFragments() {
        $this->initSourceArray(false);
        
        $SourceArray          = $this->Source_object_id__PropertyHaver_object_id_array__map;
        $SourceFragment_array = [];
        foreach ($SourceArray as $_Source_object_id => $_PropertyHaver_object_id_array) {
            $_Source = Identifier::identify($_Source_object_id);
            if (!$_Source) {
                continue;
            }
            foreach ($_PropertyHaver_object_id_array as $_PropertyHaver_object_id => $count) {
                $SourceFragment_array [] = SourceAsAliasFragment::init()
                                                                ->setSource($_Source)
                                                                ->setPropertyHaverObjectId($_PropertyHaver_object_id);
            }
        }
        return $SourceFragment_array;
    }
    /**
     * Make an array of the Properties used in this table
     *
     * @return array
     * @throws \Sm\Error\UnimplementedError
     */
    public function createPropertyFragments() {
        $PropertyArray          = $this->initPropertyArray(true)->PropertyArray;
        $PropertyFragment_array = [];
        /** @var Property $Property */
        foreach ($PropertyArray as $Property) {
            $PropertyHavers = $Property->getPropertyHavers();
            if (count($PropertyHavers) > 1) {
                throw new UnimplementedError("Cannot yet create PropertyFragments for Properties owned by multiple objects");
            }
    
            $object_id = $PropertyHavers[0]->getObjectId();
            
            
            $SourceFragment = SourceAsAliasFragment::init()
                                                   ->setPropertyHaverObjectId($object_id)
                                                   ->setSource($Property->getSource());
            $PropertyFragment_array[]
                            = PropertyFragment::init()
                                              ->setProperty($Property)
                                              ->setSourceFragment($SourceFragment);
        }
        return $PropertyFragment_array;
    }
    /**
     * Make sure the Statement is in its final form
     *
     * @param $SqlStatement
     *
     * @return mixed
     */
    public function completeStatementFormatting($SqlStatement) {
        $iterations = 10;
    
        # There's something being aliased here (probably)
        while ($iterations-- && strpos($SqlStatement, '{{')) {
            # Everything that was put in there as a placeholder should come out
            $this->replaceAliasedStringsInStatement($SqlStatement);
        }
    
        # Get rid of any caching
        $this->SqlModule->FormatterFactory->reset();
        return $SqlStatement;
    }
    public static function create(Query $Query, SqlModule $SqlModule) {
        $Instance            = new static;
        $Instance->Query     = $Query;
        $Instance->SqlModule = $SqlModule;
        return $Instance;
    }
    
    #
    ##
    #
    /**
     * Iterate through the Source array and alias any of the Sources that are used by multiple different PropertyHavers.
     * This is because if two different Entities reference the same table, usually that table has to get aliased at least once.
     *
     * @return $this
     */
    protected function aliasSources() {
        if (!$this->do_alias_sources) return $this;
        
        $src_ownr_map = $this->Source_object_id__PropertyHaver_object_id_array__map;
        foreach ($src_ownr_map as $source_id => $PropertyHaverObjectContainer) {
            foreach ($PropertyHaverObjectContainer as $object_id => $count) {
                # If there are multiple PropertyHavers that use this source, alias it
                if (!$count) continue;
                
                $alias_key = Identifier::combineObjectIds($source_id, $object_id);
                
                # If we've already aliased this Source, don't do it again
                if ($this->SqlModule->FormatterFactory->Aliases->canResolve($alias_key)) continue;
                
                #
                $_alias = Util::generateRandomString(5, Util::getAlphaCharacters(0));
                $this->SqlModule->FormatterFactory->Aliases->register($alias_key, $_alias);
            }
        }
        
        return $this;
    }
    /**
     * Initialize an array that maps the Object ID of a Source to an array of numbers indexed by the object ID of an PropertyHaver
     *
     * @param bool $redo
     *
     * @return $this
     */
    protected function initSourceArray($redo = false) {
        # If we've already done this and we aren't sure that we want to redo the process
        if (isset($this->Source_object_id__PropertyHaver_object_id_array__map) && !$redo) {
            return $this;
        }
        
        # Make sure we have all of the PropertyHavers
        $PropertyHaver_object_id__Properties_map = $this->initPropertyHaverArray()->PropertyHaver_object_id__Properties_map;
        $PropertyArray                           = $this->initPropertyArray(true)->PropertyArray;
        
        # This is the array that contains a list of PropertyHavers that use a given Source
        $this->Source_object_id__PropertyHaver_object_id_array__map = new MiniContainer;
        /** @var \Sm\Storage\Container\Mini\MiniContainer $src_ownr_map */
        $src_ownr_map = &$this->Source_object_id__PropertyHaver_object_id_array__map;
        
        
        foreach ($PropertyHaver_object_id__Properties_map as $_PropertyHaver_object_id => $_PropertyHaver_PropertyArray) {
            /**
             * @var Property $_Property
             */
            foreach ($PropertyArray as $_Property_object_id => $_Property) {
                # This is an array, indexed by source id, of the object_ids of PropertyHavers that use this Source
                $_Source    = $_Property->getSource();
                $_source_id = $_Source->getObjectId();
                
                $src_ownr_map->registerDefault($_source_id, new MiniContainer);
    
                # We register the Object ID of the PropertyHaver to be the Count of the items in the SourcePropertyHaverMap that have
                # this same Source ID so we can know whether we need to alias it or not
                # If the Count is greater than 0, we alias it
                $src_ownr_map->{$_source_id}->registerDefault($_PropertyHaver_object_id,
                                                              $src_ownr_map->{$_source_id}->count());
                # Keep track of the PropertyHaver IDs
                # The "count" lets us know if there is more than one item under this source ID
            }
        }
        $this->aliasSources();
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
        if (isset($this->PropertyArray) && !$redo) return $this;
        $this->PropertyArray = $this->getQueryProperties();
        return $this;
    }
    /**
     * Initialize an array of the properties held by each different PropertyHaver
     *
     * @param bool $redo If the PropertyHaver array has already been initialized, should we re-run this function?
     *
     * @return $this
     */
    protected function initPropertyHaverArray($redo = false) {
        if (isset($this->PropertyHaver_object_id__Properties_map) && !$redo) {
            return $this;
        }
        $PropertyArray = $this->initPropertyArray()->PropertyArray;
        /**
         * @var array $PropertyHaver_object_id__Properties_map An array mapping Source ID to the objects that are trying to use them.
         *                     If multiple classes use one Source (probably a TableSource), we probably need to alias them.
         */
        $PropertyHaver_object_id__Properties_map = [];
        foreach ($PropertyArray as $index => $item) {
            # Use $item as an array
            if ($item instanceof Property) {
                $item = [ $item ];
            } else if ($item instanceof PropertyHaver && $item instanceof Identifiable) {
                $PropertyHaver_object_id__Properties_map[ $item->getObjectId() ] = $PropertyHaver_object_id__Properties_map[ $item->getObjectId() ] ?? [];
                continue;
            } else if (!($item instanceof PropertyContainer)) {
                continue;
            }
            
            # Append the source to an array
            foreach ($item as $property) {
                static::addPropertyToPropertyHaverArray($property, $PropertyHaver_object_id__Properties_map);
            }
        }
        
        foreach (array_keys($PropertyHaver_object_id__Properties_map) as $PropertyHaver_id) {
            $this->augmentQueryForPropertyHaver(Identifier::identify($PropertyHaver_id));
        }
        $this->PropertyHaver_object_id__Properties_map = $PropertyHaver_object_id__Properties_map;
        
        # Sometimes augmenting the Query adds new Properties. This just makes sure they have been added to the Property Array
        return $this->initPropertyArray(true);
    }
    
    #
    ##
    #
    /**
     * Create the Fragment that will contain the info of a "From" statement
     *
     * @return FromFragment
     */
    protected function createFromFragment() {
        $SourceFragments = $this->createSourceFragments();
        return FromFragment::init()->setSourceFragmentArray($SourceFragments);
    }
    /**
     * For one PropertyHaver of a property, modify this query based on how they say it needs to be modified.
     * A common thing that a query might be augmented for is adding an extra condition to the "Where" clause
     *
     * @param                 $PropertyHaver
     *
     * @return $this
     */
    protected function augmentQueryForPropertyHaver(PropertyHaver $PropertyHaver) {
        #todo
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
     * Add this property to the array of (array of properties indexed by Property object_id) indexed by PropertyHaver object_id
     *
     * @param Property $Property           The property that we are dealing with. Assumed to have a TableSource as a source.
     * @param array    $PropertyHaverArray A reference to the array that we want to add this Property's information to.
     *                                     Indexed by PropertyHaver, contains an array of properties indexed by their
     *                                     object id
     *
     * @throws \Sm\Error\Error
     * @throws \Sm\Error\UnimplementedError For now, we can only use Properties that only have one PropertyHaver
     */
    private static function addPropertyToPropertyHaverArray(Property $Property, &$PropertyHaverArray) {
        /** @var Identifiable[] $PropertyHavers */
        $PropertyHavers = $Property->getPropertyHavers();
        
        if (count($PropertyHavers) > 1) {
            throw new UnimplementedError("Functionality required to interact with properties that have more than one PropertyHaver is not yet implemented.");
        } else if (count($PropertyHavers) < 1) {
            throw new Error("The property must be owned. $Property");
        }
        
        # An array, indexed by the object_id of the Source
        $PropertyHaverArray = $PropertyHaverArray ?? [];
        
        # Append the PropertyHavers to the array
        foreach ($PropertyHavers as $PropertyHaver) {
            $PropertyHaverArray[ $PropertyHaver->getObjectId() ]                             = $PropertyHaverArray[ $PropertyHaver->getObjectId() ] ??[];
            $PropertyHaverArray[ $PropertyHaver->getObjectId() ][ $Property->getObjectId() ] = $Property;
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