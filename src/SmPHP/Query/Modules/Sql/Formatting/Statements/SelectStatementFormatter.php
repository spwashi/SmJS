<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 9:45 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\Clauses\WhereClause;
use Sm\Query\Statements\SelectStatement;

class SelectStatementFormatter extends SqlQueryFormatter implements Formatter {
    /**
     * Return the item Formatted in the specific way
     *
     * @param SelectStatement $statement
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function format($statement): string {
        if (!($statement instanceof SelectStatement)) throw new InvalidArgumentException("Can only format SelectStatements");
        $select_expression_list = $this->formatSelectExpressionList($statement->getSelectedItems());
        $sources                = $this->formatFromList($statement->getFromSources());
        $from_string            = "FROM {$sources}";
        $where_string           = "";
        if ($whereClause = $statement->getWhereClause()) {
            $whereFormatter       = $this->formatterFactory->resolve(WhereClause::class);
            $formattedWhereClause = $whereFormatter->format($whereClause);
            $where_string         = "{$formattedWhereClause}";
        }
        $select_stmt_string = "SELECT {$select_expression_list} {$from_string} {$where_string}";
        
        return $select_stmt_string;
    }
    /**
     * Format the select expression based on the selected items
     *
     * @param array $selects
     *
     * @return string
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    protected function formatSelectExpressionList(array $selects): string {
        $expression_list = [];
        foreach ($selects as $item) {
            if (is_string($item)) $expression_list[] = $item;
            else throw new UnimplementedError("+ Anything but strings in the expression list");
        }
        return join(", ", $expression_list);
    }
    /**
     * Format the list of things that will form the "FROM" clause
     *
     * @param $source_array
     *
     * @return string
     */
    protected function formatFromList($source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) {
            $sources[] = $source;
        }
        return join(', ', $sources);
    }
}