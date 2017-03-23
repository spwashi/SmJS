<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 4:11 PM
 */

namespace Sm\Storage\Modules\Sql\Interpreter;


use Sm\Abstraction\Identifier\Identifiable;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Query\Interpreter\Exception\UninterpretableError;
use Sm\Query\Interpreter\QueryInterpreter;
use Sm\Query\Query;
use Sm\Storage\Modules\Sql\SqlModule;

abstract class SqlQueryInterpreter extends QueryInterpreter {
    /** @var  \Sm\Storage\Modules\Sql\SqlModule $SqlModule */
    protected $SqlModule;
    /**
     * Set the SqlModule that should be used alongside this QueryInterpreter
     *
     * @param \Sm\Storage\Modules\Sql\SqlModule $sql_module
     *
     * @return $this
     */
    public function setSqlModule(SqlModule $sql_module) {
        $this->SqlModule = $sql_module;
        return $this;
    }
    public function interpret(Query $Query) {
        if (!isset($this->SqlModule)) throw new UninterpretableError("Cannot interpret query without a SqlModule.");
        
        $query_type = $Query->getQueryType();
        $method     = "interpret_{$query_type}";
        
        if (method_exists($this, $method)) return call_user_func([ $this, $method ], $Query);
        
        
        throw new UninterpretableError("Cannot interpret query of type \"{$query_type}\".");
    }
    
    /**
     * This is what the "interpret" functions should look like.
     *
     *
     * @param \Sm\Query\Query $query
     */
    public function interpret_example(Query $query) { }
    
    /**
     * Iterate through the properties that we are trying to select and get their owners.
     *
     * @param array $Select
     *
     * @return \Sm\Abstraction\Identifier\Identifiable[]
     */
    protected static function getAllSelectOwners(array $Select) {
        /**
         * @var array $Sources An array mapping Source ID to the objects that are trying to use them.
         *                     If multiple classes use one Source (probably a TableSource), we probably need to alias them.
         */
        $Sources = [];
        foreach ($Select as $index => $item) {
            # Use $item as an array
            if ($item instanceof Property) $item = [ $item ];
            else if (!($item instanceof PropertyContainer)) continue;
            
            # Append the source to an array
            foreach ($item as $property) {
                static::mapPropertySourceToPropertyOwner($property, $Sources);
            }
        }
        return $Sources;
    }
    
    /**
     * Function to add the Source of this property to the same index
     *
     * @param Property $Property    The property that we are dealing with. Assumed to have a TableSource as a source.
     * @param array    $SourceArray A reference to the array that we want to add this Property's information to.
     *
     * @throws \Sm\Error\Error
     * @throws \Sm\Error\UnimplementedError For now, we can only use Properties that only have one Owner
     */
    private static function mapPropertySourceToPropertyOwner(Property $Property, &$SourceArray) {
        /** @var Identifiable[] $owners */
        $owners = $Property->getOwners();
        
        if (count($owners) > 1) throw new UnimplementedError("Functionality required to interact with properties that have more than one Owner is not yet implemented.");
        else if (count($owners) < 1) throw new Error("The property must be owned. $Property");
        
        $Source    = $Property->getSource();
        $source_id = $Source->getObjectId();
        
        if (!isset($source_id)) throw new Error("The requested Source cannot be identified.");
        
        # An array, indexed by the object_id of the Source
        $SourceArray = $SourceArray ?? [];
        
        # Append the owners to the array
        foreach ($owners as $owner) {
            $SourceArray[ $owner->getObjectId() ]   = $SourceArray[ $owner->getObjectId() ] ??[];
            $SourceArray[ $owner->getObjectId() ][] = $Property;
        }
    }
    
}