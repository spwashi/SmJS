<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:11 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Clauses;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\Clauses\WhereClause;

/**
 * Class WhereClauseFormatter
 *
 * Formats Where clauses
 *
 * @package Sm\Query\Modules\Sql\Formatting\Clause
 */
class WhereClauseFormatter extends SqlQueryFormatter {
    /**
     * @param WhereClause $whereClause
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Query\Modules\Sql\Formatting\Clauses\IncompleteClauseException
     */
    public function format($whereClause): string {
        if (!($whereClause instanceof WhereClause)) throw new InvalidArgumentException('Can only format WhereClauses');
        $where_clause_str = "WHERE\t";
        $conditions       = $whereClause->getConditions();
        if (!count($conditions)) throw new IncompleteClauseException("There are no conditions to the Where clause.");
        
        foreach ($conditions as $index => $condition) {
            if ($index !== 0) $where_clause_str .= ' AND ';
    
            $where_clause_str .= $this->formatComponent($condition);
        }
        return $where_clause_str;
    }
}