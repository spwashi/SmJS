<?php
/**
 * User: Sam Washington
 * Date: 7/6/17
 * Time: 10:21 PM
 */

namespace Sm\Query\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Evaluation\EvaluableStatement;
use Sm\Data\Source\DataSource;
use Sm\Query\Statements\Clauses\WhereClause;

/**
 * Class SelectQuery
 *
 * @package Sm\Query\Types
 * @method static SelectStatement init()
 */
class SelectStatement extends QueryComponent {
    protected $selected_items   = [];
    protected $where_conditions = [];
    /** @var  WhereClause $where Contains the conditions & whatnot that we will evaluate for each row */
    protected $where;
    /** @var  array The sources that we are going to use. Only used if it's set, otherwise we use the selected items */
    protected $from_sources = [];
    public function __construct(...$items) {
        $this->select(...$items);
    }
    /**
     * Select items (often Schemas) Usually these are going to be things like Columns
     *
     * @param mixed ...$select_items
     *
     * @return $this
     */
    public function select(...$select_items) {
        $this->selected_items = array_merge($this->selected_items, $select_items);
        foreach ($select_items as $item) {
            if (is_string($item)) continue;
            $this->from_sources[] = $this->getSourceGarage()->resolve($item);
        }
        return $this;
    }
    public function from(...$from_sources) {
        foreach ($from_sources as $source) {
            if (is_string($source)) {
                if (strpos($source, '\\')) throw new InvalidArgumentException("Cannot set Source to be a classname");
            } else if (!($source instanceof DataSource)) {
                throw new InvalidArgumentException("Cannot set source to be something that is not a DataSource");
            }
        }
        $this->from_sources = $from_sources;
        return $this;
    }
    /**
     * Return the sources used
     */
    public function getFromSources() {
        return $this->from_sources;
    }
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
     * Get the items that are going to be selected
     *
     * @return array
     */
    public function getSelectedItems(): array {
        return $this->selected_items;
    }
    /**
     * Get the WhereClause used in this Select Statement
     *
     * @return null|\Sm\Query\Statements\Clauses\WhereClause
     */
    public function getWhereClause():?WhereClause {
        return count($this->where_conditions)
            ? new WhereClause(...$this->where_conditions)
            : null;
    }
}