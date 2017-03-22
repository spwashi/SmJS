<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 3:45 PM
 */

namespace Sm\Storage\Modules\Sql\MySql;


use Sm\Query\Query;
use Sm\Storage\Modules\Sql\SqlQueryInterpreter;

/**
 * Class MysqlQueryInterpreter
 *
 * Meant to handle the execution and interpretation of Mysql Queries
 *
 * @package Sm\Storage\Modules\Sql\MySql
 */
class MysqlQueryInterpreter extends SqlQueryInterpreter {
    protected static function interpret_select(Query $Query) {
        $objects = [];
        $Query->getSelectArray();
    }
}