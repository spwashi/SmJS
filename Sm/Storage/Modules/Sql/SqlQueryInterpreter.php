<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 4:11 PM
 */

namespace Sm\Storage\Modules\Sql;


use Sm\Query\Interpreter\Exception\UninterpretableError;
use Sm\Query\Interpreter\QueryInterpreter;
use Sm\Query\Query;

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
}