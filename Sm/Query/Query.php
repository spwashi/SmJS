<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:18 PM
 */

namespace Sm\Query;

use Sm\Abstraction\Factory\HasFactoryContainerTrait;
use Sm\Entity\Property\PropertyContainer;
use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Error\WrongArgumentException;
use Sm\Query\Interpreter\QueryInterpreterFactory;
use Sm\Storage\Source\Exception\UnauthorizedConnectionError;
use Sm\Storage\Source\NullSource;
use Sm\Storage\Source\Source;
use Sm\Storage\Source\SourceHaver;

/**
 * Class Query
 *
 * A class that represents the most generic aspects of Queries that we can represent.
 *
 * todo THINK ABOUT THIS AF
 *
 * @package Sm\Query
 *
 * @method static Query select_(...$items)
 */
class Query {
    use HasFactoryContainerTrait;
    
    const QUERY_TYPE_SELECT = 'select';
    const QUERY_TYPE_INSERT = 'insert';
    const QUERY_TYPE_UPDATE = 'update';
    
    /** @var array $select_array An array of properties or whatever that we want to select */
    protected $select_array = [];
    /** @var array $update_array An array of the properties that we want to update */
    protected $update_array = [];
    /** @var array $insert_array An array of properties that we want to insert */
    protected $insert_array = [];
    /** @var array[] $values_array An array of arrays of the values that we want to insert */
    protected $values_array = [];
    /** @var null|string $query_type This is the type of Query we are executing */
    protected $query_type = null;
    /** @var  \Sm\Query\Where $where The overall Where clause that accompanies this Query */
    protected $where;
    
    #
    ##  Getters and Setters
    #
    /**
     * Get the type of Query we are going to be executing
     *
     * @return null|string
     */
    public function getQueryType() {
        return $this->query_type;
    }
    /**
     * Get the array of things that we want to select.
     *
     * @return array
     */
    public function getSelectArray(): array {
        return $this->select_array;
    }
    public function getUpdateArray(): array {
        return $this->update_array;
    }
    public function getInsertArray(): array {
        return $this->insert_array;
    }
    public function getValuesArray(): array {
        return $this->values_array;
    }
    /**
     * Get the Where clause of the Query
     *
     * @return Where
     */
    public function getWhere() {
        return $this->where;
    }
    #
    ## Public Query methods
    #
    /**
     * Set the Properties that we want to select
     *
     * @param array ...$items
     *
     * @return $this
     * @throws \Sm\Error\WrongArgumentException
     */
    public function select(...$items) {
        $this->query_type = $this->query_type ?? static::QUERY_TYPE_SELECT;
        foreach ($items as $index => $item) {
            
            # todo vague error
            try {
                $this->canSelectItem($item, true);
            } catch (Error $e) {
                throw new WrongArgumentException(
                    "Argument {$index} cannot be queried --  " .
                    "Please check that it is a (or an array of) SourceHaver Object(s) " .
                    "and the Source is properly authenticated and connected. \n" .
                    "Additionally, {$e->getMessage()}");
                
            }
            $this->select_array[] = $item;
        }
        return $this;
    }
    /**
     * Set the Where clause of the query
     *
     * @param \Sm\Query\Where $where
     *
     * @return $this
     * @internal param array ...$items
     *
     */
    public function where(Where $where) {
        if (!isset($this->where)) {
            $this->where = $where;
        } else {
            $this->where->appendConditions($where->getRawConditionsArray());
        }
        
        return $this;
    }
    /**
     * Update the values of some properties on whatever platform
     *
     * @param array ...$items
     *
     * @return \Sm\Query\Query
     */
    public function update(...$items) {
        $this->query_type   = $this->query_type ?? static::QUERY_TYPE_UPDATE;
        $this->update_array = array_merge($this->update_array, array_filter($items));
        return $this;
    }
    /**
     * Insert some properties (or update them) on whatever platform
     *
     * @param array ...$items
     *
     * @return $this
     */
    public function insert(...$items) {
        $this->query_type   = $this->query_type ?? static::QUERY_TYPE_INSERT;
        $this->insert_array = array_merge($this->insert_array, array_filter($items));
        return $this;
    }
    /**
     * Set the values that we want to insert
     *
     * @param array ...$items
     *
     * @return $this
     */
    public function values(...$items) {
        $this->values_array = array_merge($this->values_array, $items);
        return $this;
    }
    /**
     * Run the Query, delegating subqueries to the proper source
     *
     * @return mixed
     * @throws \Sm\Error\Error
     * @throws \Sm\Error\UnimplementedError
     */
    public function run() {
        $SourceArray = $this->getSourcesUsed();
    
        if (count($SourceArray) > 1) {
            throw new UnimplementedError("Cannot query across root sources");
        } else if (!count($SourceArray)) {
            throw new Error("There is no Source to execute this Query.");
        }
        
        /** @var Source $RootSource The Source that is going to be handling this Query */
        $RootSource              = $SourceArray[ key($SourceArray) ];
        $Factories               = $this->getFactoryContainer();
        $QueryInterpreterFactory = $Factories->resolve(QueryInterpreterFactory::class);
        /** @var \Sm\Query\Interpreter\QueryInterpreter $QueryInterpreter */
        $QueryInterpreter = $QueryInterpreterFactory->build($RootSource);
        if (isset($QueryInterpreter)) {
            return $QueryInterpreter->interpret($this);
        }
        return null;
    }
    
    #
    ## Class methods
    #
    /**
     * Static constructor for Query
     *
     * @return static
     */
    public static function init() {
        return new static;
    }
    /**
     * Allow us to call the standard methods with a shortcut.
     * e.g. Query::init()->select   -->   Query::select()
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws \Sm\Error\Error
     * @todo this might lead to a bug with private/protected methods?
     *
     */
    public static function __callStatic($name, $arguments) {
        $Self = new static;
        
        $strlen_name          = strlen($name);
        $ends_with_underscore = strpos($name, '_', $strlen_name - 1);
        if ($ends_with_underscore) {
            $name = substr($name, 0, $strlen_name - 1);
            if (method_exists($Self, $name)) {
                return call_user_func_array([ $Self, $name ], $arguments);
            }
        }
        throw new Error("There is no method {$name} in this class.");
    }
    /**
     * Functions to check to see if we an select an item.
     * Depends on the Source, etc.
     *
     * @param      $item
     *
     * @param bool $throw_an_error
     *
     * @return bool
     * @throws \Sm\Error\WrongArgumentException
     * @throws \Sm\Storage\Source\Exception\UnauthorizedConnectionError
     */
    protected function canSelectItem($item, $throw_an_error = false) {
        if ($item instanceof SourceHaver) {
            $Source           = $item->getSource();
            $source_is_active = $Source && $Source->isAuthenticated();
            if (!$source_is_active && $throw_an_error) {
                throw new UnauthorizedConnectionError("The Connection to this object's source is invalid.");
            }
            return $source_is_active;
        }
        if ($item instanceof \Traversable) {
            foreach ($item as $value) {
                if (!$this->canSelectItem($value, true)) {
                    return false;
                }
            }
            return true;
        }
        
        $source_haver_class = SourceHaver::class;
        if ($throw_an_error) {
            throw new WrongArgumentException("Item must be an instance of {$source_haver_class}");
        }
        
        return false;
    }
    /**
     * Get an array of all of the Sources used by this class.
     * The array should be indexed by Source Identifier.
     *
     * @return array
     */
    protected function getSourcesUsed(): array {
        $components = array_merge($this->select_array,
                                  $this->insert_array,
                                  $this->update_array);
        /**
         * @var Source[] $Sources An array, indexed by object_id, of the Sources used
         */
        $Sources = [];
        foreach ($components as $component) {
            if ($component instanceof PropertyContainer) {
                foreach ($component as $property) {
                    $RootSource = static::getRootSourceFromSourceHaver($property);
                    if ($RootSource) {
                        $Sources[ $RootSource->getObjectId() ] = $RootSource;
                    }
                }
            } else {
                $RootSource = static::getRootSourceFromSourceHaver($component);
                if ($RootSource) {
                    $Sources[ $RootSource->getObjectId() ] = $RootSource;
                }
            }
        }
    
        # Don't return the same Source twice
        $Sources = array_unique($Sources);
        return $Sources;
    }
    /**
     * For everything that has a source, get the Root Source of that
     *
     * @param \Sm\Storage\Source\SourceHaver $SourceHaver
     *
     * @return null|\Sm\Storage\Source\Source
     */
    protected static function getRootSourceFromSourceHaver(SourceHaver $SourceHaver) {
        if (!($SourceHaver instanceof SourceHaver)) {
            return null;
        }
        $RootSource = $SourceHaver->getSource()->getRootSource();
        
        # NullSources don't matter. Skip over them.
        if ($RootSource instanceof NullSource) {
            return null;
        }
        
        
        $Sources[ $RootSource->getName() ] = $RootSource;
        return $RootSource;
    }
}