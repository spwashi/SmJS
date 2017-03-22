<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:18 PM
 */

namespace Sm\Query;

use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Error\WrongArgumentException;
use Sm\EvaluableStatement\EvaluableStatement;
use Sm\Resolvable\Resolvable;
use Sm\Storage\Source\Exception\UnauthorizedConnectionError;
use Sm\Storage\Source\NullSource;
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
class Query extends Resolvable {
    protected $select_array = [];
    protected $update       = [];
    protected $delete       = [];
    protected $create       = [];
    
    
    protected $conditions = [];
    /**
     * Set the Properties that we want to select
     *
     * @param array ...$items
     *
     * @return $this
     * @throws \Sm\Error\WrongArgumentException
     */
    public function select(...$items) {
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
            
            if ($item instanceof SourceHaver) {
                $this->select_array[] = $item;
            } else if ($item instanceof \Traversable) {
                # Append the iterated values
                $this->select_array += iterator_to_array($item, false);
            }
        }
        return $this;
    }
    /**
     * Set the Where clause of the query
     *
     * @param array ...$items
     *
     * @return $this
     */
    public function where(EvaluableStatement ...$items) {
        $this->conditions += $items;
        return $this;
    }
    
    
    /**
     * Run the Query, delegating subqueries to the proper source
     *
     * @return mixed
     * @throws \Sm\Error\UnimplementedError
     */
    public function resolve() {
        $SourceArray = $this->getSourcesUsed();
        if (count($SourceArray) > 1) throw new UnimplementedError("Cannot query across root sources");
        
        /** @var \Sm\Storage\Source\Source $RootSource The Source that is going to be handling this Query */
        $RootSource = $SourceArray[ key($SourceArray) ];
        
        
        return $this;
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
            if (method_exists($Self, $name)) return call_user_func_array([ $Self, $name ], $arguments);
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
            if (!$source_is_active && $throw_an_error) throw new UnauthorizedConnectionError("The Connection to this object's source is invalid.");
            return $source_is_active;
        }
        if ($item instanceof \Traversable) {
            foreach ($item as $value) {
                if (!$this->canSelectItem($value, true)) return false;
            }
            return true;
        }
        
        $source_haver_class = SourceHaver::class;
        if ($throw_an_error) throw new WrongArgumentException("Item must be an instance of {$source_haver_class}");
        
        return false;
    }
    /**
     * Get an array of all of the Sources used by this class.
     * The array should be indexed by Source Identifier.
     *
     * @return array
     */
    protected function getSourcesUsed(): array {
        $components = $this->select_array + $this->update + $this->conditions;
        $Sources    = [];
        foreach ($components as $component) {
            if (!($component instanceof SourceHaver)) continue;
            $RootSource = $component->getSource()->getRootSource();
            
            # NullSources don't matter. Skip over them.
            if ($RootSource instanceof NullSource) continue;
            
            # todo reconsider naming scheme for sources
            ## todo IDENTIFIER
            $Sources[ $RootSource->getName() ] = $RootSource;
        }
        $Sources = array_unique($Sources);
        return $Sources;
    }
}