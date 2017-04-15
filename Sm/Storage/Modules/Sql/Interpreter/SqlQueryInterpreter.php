<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 4:11 PM
 */

namespace Sm\Storage\Modules\Sql\Interpreter;


use Sm\Entity\EntityType;
use Sm\Query\Interpreter\Exception\UninterpretableError;
use Sm\Query\Interpreter\QueryInterpreter;
use Sm\Query\Query;
use Sm\Storage\Modules\Sql\MySql\Interpreter\InsertStatementSubInterpreter;
use Sm\Storage\Modules\Sql\MySql\Interpreter\SelectStatementSubInterpreter;
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
        if (!isset($this->SqlModule)) {
            throw new UninterpretableError("Cannot interpret query without a SqlModule.");
        }
        
        
        switch ($Query->getQueryType()) {
            case 'select':
                $Interpreter = SelectStatementSubInterpreter::create($Query, $this->SqlModule);
                break;
            case 'insert':
                $Interpreter = InsertStatementSubInterpreter::create($Query, $this->SqlModule);
                break;
            default:
                return [];
        }
        
        return $Interpreter->execute();
    }
    
    #region Completing the Query
    /**
     * For one owner of a property, modify this query based on how they say it needs to be modified.
     * A common thing that a query might be augmented for is adding an extra condition to the "Where" clause
     *
     * @param                 $Owner
     * @param \Sm\Query\Query $Query
     * @param                 $property_array
     */
    private function augmentQueryForOwner($Owner, Query $Query) {
        if ($Owner instanceof EntityType) {
            $Owner->augmentQuery($this, $Query);
        }
    }
    #endregion
}