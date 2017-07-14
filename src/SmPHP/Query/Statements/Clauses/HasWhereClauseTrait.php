<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:07 PM
 */

namespace Sm\Query\Statements\Clauses;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Evaluation\EvaluableStatement;

/**
 * Trait HasWhereClauseTrait
 *
 * Represents Statements that have WhereClauses
 *
 * @package Sm\Query\Statements
 */
trait HasWhereClauseTrait {
    /** @var array The Conditions that are going to be ANDed together as part of the "WHERE" clause */
    protected $where_conditions = [];
    /**
     * Set the Conditions that are going to be a part of the "WHERE" clause
     *
     * @param EvaluableStatement[]|array ...$conditions
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function where(...$conditions) {
        foreach ($conditions as $condition) {
            if (!($condition instanceof EvaluableStatement)) throw new InvalidArgumentException("Can only use Conditions in WHERE clauses.");
            $this->where_conditions[] = $condition;
        }
        
        return $this;
    }
    /**
     * Get the WhereClause used in this Statement
     *
     * @return null|\Sm\Query\Statements\Clauses\WhereClause
     */
    public function getWhereClause():?WhereClause {
        return count($this->where_conditions)
            ? new WhereClause(...$this->where_conditions)
            : null;
    }
}