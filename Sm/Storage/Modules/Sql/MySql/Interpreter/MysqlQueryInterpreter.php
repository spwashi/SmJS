<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 3:45 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Interpreter\SqlQueryInterpreter;

/**
 * Class MysqlQueryInterpreter
 *
 * Meant to handle the execution and interpretation of Mysql Queries
 *
 * #todo make this into multiple classes.
 *
 * @package Sm\Storage\Modules\Sql\MySql
 */
class MysqlQueryInterpreter extends SqlQueryInterpreter {
    protected function createSelectResultObject($owner_array, array $rows) { }
    /**
     * Execute the Select Statement
     *
     * @param $SelectStatement
     *
     * @return array
     */
    protected function executeSelectStatement($SelectStatement) {
        echo "{$SelectStatement}\n\n";
        /** @var \Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource $DatabaseSource */
        $DatabaseSource = $this->SqlModule->getDatabaseSource();
        # todo Variables being bound
        $sth = $DatabaseSource->getConnection()
                              ->prepare("$SelectStatement");
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Create an object to represent the results of a Insert Query
     *
     * @param                    $owner_array
     * @param PropertyFragment[] $PropertyFragment_array
     * @param array              $rows
     *
     * @return array
     * @internal param array $owners_by_property
     */
    protected function createInsertResultObject($owner_array, array $rows = null) {
        return [];
    }
    /**
     * Execute a Insert Statement
     *
     * @param $InsertStatement
     *
     * @return mixed
     */
    protected function executeInsertStatement($InsertStatement) {
        echo "{$InsertStatement}\n\n";
        /** @var \Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource $DatabaseSource */
        $DatabaseSource = $this->SqlModule->getDatabaseSource();
        # todo Variables being bound
        #$sth = $DatabaseSource->getConnection()->prepare("$InsertStatement");
        return [];
        #$sth->execute();
        #return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}