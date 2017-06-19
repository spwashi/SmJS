<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:18 PM
 */

namespace Sm\Process\Query;

use Sm\Core\Error\Error;
use Sm\Core\Error\UnimplementedError;
use Sm\Core\Error\WrongArgumentException;
use Sm\Core\Factory\HasFactoryContainerTrait;
use Sm\Core\Util;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Source\DataSource;
use Sm\Data\Source\Exception\UnauthorizedConnectionError;
use Sm\Data\Source\NullDataSource;
use Sm\Data\Source\SourceHaver;
use Sm\Process\Query\Interpreter\QueryInterpreterFactory;

/**
 * Class Query
 *
 * A class that represents the most generic aspects of Queries that we can represent.
 *
 * todo THINK ABOUT THIS AF
 *
 * @package Sm\Process\Query
 *
 * @property-read Property[]                    $select
 * @property-read Property[]                    $delete
 * @property-read Property[]                    $insert
 * @property-read Property[]                    $update
 * @property-read mixed                         $create_item
 * @property-read array                         $values
 * @property-read \Sm\Process\Query\WhereClause $WhereClause
 *
 * @method  static Query select(...$items) Properties to select.
 * @method  static Query delete(...$items) Properties to delete.
 * @method  static Query insert(...$items) Properties to insert. Values from these are included
 * @method  static Query update(...$items) Properties to update (updates from their values)
 * @method  static Query values(...$items) Arrays of values to insert.
 *
 * @method static Query val(...$items)
 */
class Query {
    use HasFactoryContainerTrait;
    
    # region properties
    # region query types
    const QUERY_TYPE_SELECT = 'select';
    const QUERY_TYPE_INSERT = 'insert';
    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_DELETE = 'delete';
    const QUERY_TYPE_CREATE = 'create_table';
    /** @var null|string $query_type This is the type of Query we are executing */
    protected $query_type = null;
    # endregion
    
    /** @var  \Sm\Process\Query\WhereClause $WhereClause The overall Where clause that accompanies this Query */
    protected $WhereClause;
    /** @var array An array of the Properties that this Query is aware that it uses */
    protected $PropertyArray = [];
    
    # region Component Arrays
    /** @var array $select_array An array of properties or whatever that we want to select */
    protected $select_array = [];
    /** @var array $delete_array An array of properties or whatever that we want to delete */
    protected $delete_array = [];
    /** @var array $update_array An array of the properties that we want to update */
    protected $update_array = [];
    /** @var array $insert_array An array of properties that we want to insert */
    protected $insert_array = [];
    /** @var array[] $values_array An array of arrays of the values that we want to insert */
    protected $values_array = [];
    /** @var array[] $create An array of the things that we want to create */
    protected $create = [];
    # endregion
    # endregion
    
    
    #
    ##  Constructor
    public function __call($name, $arguments) {
        if (in_array($name, [ 'select', 'update', 'delete', 'insert' ])) {
            $PropertyArray       = $this->runStdQueryTypeFunction($name, $arguments);
            $this->PropertyArray = array_merge($this->PropertyArray, $PropertyArray);
            return $this;
        }
        
        if ($name === 'values') {
            foreach ($arguments as $index => $array) {
                if (!is_array($array)) throw new WrongArgumentException("Can only insert using arrays");
            }
            $this->values_array = array_merge($this->values_array, $arguments);
            return $this;
        }
    
        throw new WrongArgumentException("There is no method {$name}");
    }
    public function __get($name) {
        if (in_array($name, [ 'select', 'update', 'delete', 'insert', 'values', ])) {
            return $this->{"{$name}_array"} ?? [];
        }
        if ($name === 'create_item') return $this->create;
        if ($name === 'WhereClause') return $this->WhereClause;
        return null;
    }
    /**
     * Get the type of Query we are going to be executing
     *
     * @return null|string
     */
    public function getQueryType() {
        return $this->query_type;
    }
    
    #
    ##  Getters and Setters
    /**
     * Set the Where clause of the query
     *
     * @param \Sm\Process\Query\WhereClause $where
     *
     * @return $this
     */
    public function where(WhereClause $where) {
        if (!isset($this->WhereClause)) {
            $this->WhereClause = $where;
        } else {
            $this->WhereClause->appendConditions($where->getRawConditionsArray());
        }
        
        return $this;
    }
    public function create($item) {
        $this->query_type = static::QUERY_TYPE_CREATE;
        $this->create     = $item;
        return $this;
    }
    #
    ## Public Query methods
    /**
     * Run the Query, delegating subqueries to the proper source
     *
     * @return mixed
     * @throws \Sm\Core\Error\Error
     * @throws \Sm\Core\Error\UnimplementedError
     */
    public function run() {
        $SourceArray = $this->getSourcesUsed();
    
        if (count($SourceArray) > 1) {
            throw new UnimplementedError("Cannot query across root sources");
        } else if (!count($SourceArray)) {
            throw new Error("There is no DataSource to execute this Query.");
        }
    
    
        /** @var DataSource $RootSource The DataSource that is going to be handling this Query */
        $RootSource              = $SourceArray[ key($SourceArray) ];
        $Factories               = $this->getFactoryContainer();
        $QueryInterpreterFactory = $Factories->resolve(QueryInterpreterFactory::class);
    
        /** @var \Sm\Process\Query\Interpreter\QueryInterpreter $QueryInterpreter */
        $QueryInterpreter = $QueryInterpreterFactory->build($RootSource);
    
        $Owners = $this->getPropertyHaversFromArray($this->PropertyArray);
        $Query  = $this;
        foreach ($Owners as $PropertyHaver) {
            if ($PropertyHaver instanceof QueryAugmentor) {
                $Query = $PropertyHaver->augmentQuery($Query);
            }
        }
    
        return isset($QueryInterpreter) ? $QueryInterpreter->interpret($Query) : null;
    }
    /**
     * Static constructor for Query
     *
     * @return static
     */
    public static function init() {
        return new static;
    }
    
    #
    ## Class methods
    /**
     * Functions to check to see if we can use a property.
     * Depends on the DataSource, etc.
     *
     * @param      $item
     *
     * @param bool $throw_an_error
     *
     * @return bool
     * @throws \Sm\Core\Error\WrongArgumentException
     * @throws \Sm\Data\Source\Exception\UnauthorizedConnectionError
     */
    protected function canUseProperties($item, $throw_an_error = false) {
        
        # We assume that it has a source
        if ($item instanceof SourceHaver) {
            $Source = $item->getSource();
        } else if ($item instanceof DataSource) {
            $Source = $item;
        }
    
    
        if (isset($Source)) {
            $source_is_active = $Source && $Source->isAuthenticated();
            if (!$source_is_active && $throw_an_error) {
                throw new UnauthorizedConnectionError("The Connection to this object's source is invalid.");
            }
            return $source_is_active;
        }
    
    
        # If we're asking about an array, iterate through
        if ($item instanceof \Traversable || is_array($item)) {
            foreach ($item as $value) {
                if (!$this->canUseProperties($value, $throw_an_error)) return false;
            }
            return true;
        }
        
        $source_haver_class = SourceHaver::class;
        
        # Throw an error if we want because the item can't be used
        if ($throw_an_error) throw new WrongArgumentException("Item must be an instance of {$source_haver_class}");
        
        return false;
    }
    /**
     * Get an array of the Properties used to build this Query
     *
     * @return Property[]
     */
    protected function getReferencedProperties() {
        return array_unique(array_merge(
                                $this->select_array,
                                $this->update_array,
                                $this->delete_array,
                                $this->insert_array
                            ));
    }
    protected function getPropertyHaversFromArray($PropertyArray) {
        $PropertyHavers = [];
        foreach ($PropertyArray as $index => $item) {
            if (!($item instanceof Property)) continue;
            $PropertyHavers[] = $item->getPropertyHavers();
        }
    
        if (!count($PropertyHavers)) return [];
        
        return array_unique(array_merge(...$PropertyHavers));
    }
    /**
     * Get an array of all of the Sources used by this class.
     * The array should be indexed by DataSource Identifier.
     *
     * @return array
     */
    protected function getSourcesUsed(): array {
        $components   = $this->getReferencedProperties();
        $components[] = $this->create;
        $components   = array_filter($components);
        /**
         * @var DataSource[] $Sources An array, indexed by object_id, of the Sources used
         */
        $Sources = [];
    
    
        foreach ($components as $component) {
    
            if ($component instanceof DataSource) {
                $RootSource = $component->getRootSource();
            } else if ($component instanceof SourceHaver) {
                $RootSource = static::getRootSourceFromSourceHaver($component);
            } else {
                $_type = Util::getShapeOfItem($component);
                throw new WrongArgumentException("Cannot get DataSource from type {$_type}");
            }
        
            if ($RootSource) $Sources[ $RootSource->getObjectId() ] = $RootSource;
        }
    
        # Don't return the same DataSource twice
        $Sources = array_unique($Sources);
        return $Sources;
    }
    /**
     * For everything that has a source, get the Root DataSource of that
     *
     * @param \Sm\Data\Source\SourceHaver $SourceHaver
     *
     * @return null|\Sm\Data\Source\DataSource
     */
    protected static function getRootSourceFromSourceHaver(SourceHaver $SourceHaver) {
        if (!($SourceHaver instanceof SourceHaver)) {
            return null;
        }
        $RootSource = $SourceHaver->getSource()->getRootSource();
        
        # NullSources don't matter. Skip over them.
        if ($RootSource instanceof NullDataSource) {
            return null;
        }
        
        
        $Sources[ $RootSource->getName() ] = $RootSource;
        return $RootSource;
    }
    /**
     * Function meant to handle default functionality of basic query types. Standin for select, update, delete, insert functions
     *
     * @param $name
     * @param $arguments
     *
     * @return Property[]
     * @throws \Sm\Core\Error\WrongArgumentException
     */
    private function runStdQueryTypeFunction($name, $arguments) {
        $this->query_type = $this->query_type ?? $name;
        $array_name       = "{$name}_array";
        
        if (in_array($name, [ 'select', 'update', 'delete', 'insert' ])) {
            # Flatten the Properties. This gets all properties from the PropertyContainer.
            $arguments = self::flattenPropertyArray($arguments);
        }
        
        # Check to see if all of the items are OK.
        try {
            $this->canUseProperties($arguments, true);
        } catch (Error $e) {
            throw new WrongArgumentException(
                "Argument cannot be queried --  " .
                "Please check that it is a (or an array of) SourceHaver Object(s) " .
                "and the DataSource is properly authenticated and connected. \n", null, $e);
            
        }
        
        # Merge the properties with the existent array
        $this->$array_name   = array_unique(array_merge($this->$array_name, array_filter($arguments)));
        $this->PropertyArray = array_merge($this->PropertyArray, array_filter($arguments));
        return $arguments;
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