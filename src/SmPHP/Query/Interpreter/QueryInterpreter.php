<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 3:33 PM
 */

namespace Sm\Query\Interpreter;

use Sm\Query\Query;

/**
 * Class QueryInterpreter
 *
 * A class that handles Queries. Based on the things described by the Query, the QueryInterpreter returns the result
 * that you are looking for.
 *
 * @package Sm\Query
 */
abstract class QueryInterpreter {
    /**
     * Given a Query, produce some sort of Output based on it
     *
     * @param \Sm\Query\Query $Query
     *
     * @return mixed
     */
    abstract public function interpret(Query $Query);
}