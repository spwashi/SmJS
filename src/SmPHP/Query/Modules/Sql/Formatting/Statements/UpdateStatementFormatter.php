<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:40 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\UpdateStatement;

/**
 * Class UpdateStatementFormatter
 *
 * Formatter for Update Statements
 *
 * @package Sm\Query\Modules\Sql\Formatting\Statements
 */
class UpdateStatementFormatter extends SqlQueryFormatter {
    public function format($statement): string {
        if (!($statement instanceof UpdateStatement)) throw new InvalidArgumentException("Can only format UpdateStatements");
        
        $update_expression_list = $this->formatUpdateExpressionList($statement->getUpdatedItems());
        $where_string           = $this->formatterFactory->format($statement->getWhereClause());
        $source_string          = $this->formatSourceList($statement->getIntoSources());
        
        $update_stmt = "UPDATE {$source_string} SET {$update_expression_list} \n {$where_string}";
        
        return $update_stmt;
    }
    protected function formatUpdateExpressionList(array $updates) {
        $expression_list = [];
        foreach ($updates as $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $expression_list[] = "{$key} = {$value}";
                }
            } else throw new UnimplementedError("+ Anything but associative in the expression list");
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
    protected function formatSourceList($source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) $sources[] = $source;
        return join(', ', $sources);
    }
}