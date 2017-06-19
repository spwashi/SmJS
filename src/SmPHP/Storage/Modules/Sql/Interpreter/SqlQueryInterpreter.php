<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 4:11 PM
 */

namespace Sm\Storage\Modules\Sql\Interpreter;


use Sm\Core\Error\UnimplementedError;
use Sm\Core\Error\WrongArgumentException;
use Sm\Data\ORM\EntityType\EntityType;
use Sm\Process\Query\Interpreter\Exception\UninterpretableError;
use Sm\Process\Query\Interpreter\QueryInterpreter;
use Sm\Process\Query\Query;
use Sm\Storage\Modules\Sql\MySql\Interpreter\CreateTableSourceQuerySubInterpreter;
use Sm\Storage\Modules\Sql\MySql\Interpreter\DeleteQuerySubInterpreter;
use Sm\Storage\Modules\Sql\MySql\Interpreter\InsertQuerySubInterpreter;
use Sm\Storage\Modules\Sql\MySql\Interpreter\SelectQuerySubInterpreter;
use Sm\Storage\Modules\Sql\MySql\Interpreter\UpdateQuerySubInterpreter;
use Sm\Storage\Modules\Sql\SqlStandardModule;

abstract class SqlQueryInterpreter extends QueryInterpreter {
    /** @var  \Sm\Storage\Modules\Sql\SqlStandardModule $SqlModule */
    protected $SqlModule;
    /**
     * Set the SqlModule that should be used alongside this QueryInterpreter
     *
     * @param \Sm\Storage\Modules\Sql\SqlStandardModule $sql_module
     *
     * @return $this
     */
    public function setSqlModule(SqlStandardModule $sql_module) {
        $this->SqlModule = $sql_module;
        return $this;
    }
    
    public function interpret(Query $Query) {
        if (!isset($this->SqlModule)) throw new UninterpretableError("Cannot interpret query without a SqlModule.");
    
        $queryType = $Query->getQueryType();
        switch ($queryType) {
            case Query::QUERY_TYPE_SELECT:
                $Interpreter = SelectQuerySubInterpreter::init($Query, $this->SqlModule);
                break;
            case Query::QUERY_TYPE_INSERT:
                $Interpreter = InsertQuerySubInterpreter::init($Query, $this->SqlModule);
                break;
            case Query::QUERY_TYPE_DELETE:
                $Interpreter = DeleteQuerySubInterpreter::init($Query, $this->SqlModule);
                break;
            case Query::QUERY_TYPE_UPDATE:
                $Interpreter = UpdateQuerySubInterpreter::init($Query, $this->SqlModule);
                break;
            case Query::QUERY_TYPE_CREATE:
                $Item = $Query->create_item;
        
                if (!is_object($Item)) {
                    $type = gettype($Item);
                    throw new WrongArgumentException("Not sure how to create objects of type '{$type}'");
                }
                $Interpreter = CreateTableSourceQuerySubInterpreter::init($Query, $this->SqlModule);
                break;
            default:
                $type_message = !empty($queryType) ? "of type '{$queryType}'" : "that don't have an associated interpreter.";
                throw new UnimplementedError("Do not know how to handle queries $type_message");
        }
        
        return $Interpreter->execute();
    }
    
    #region Completing the Query
    /**
     * For one PropertyHaver of a property, modify this query based on how they say it needs to be modified.
     * A common thing that a query might be augmented for is adding an extra condition to the "Where" clause
     *
     * @param                 $PropertyHaver
     * @param \Sm\Process\Query\Query $Query
     * @param                 $property_array
     */
    private function augmentQueryForPropertyHaver($PropertyHaver, Query $Query) {
        if ($PropertyHaver instanceof EntityType) {
            $PropertyHaver->augmentQuery($this, $Query);
        }
    }
    #endregion
}