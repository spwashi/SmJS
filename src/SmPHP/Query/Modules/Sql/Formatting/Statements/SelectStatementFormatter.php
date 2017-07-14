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
use Sm\Query\Modules\Sql\Formatting\Proxy\ColumnFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\TableFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
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
        $where_string           = $this->formatterFactory->format($statement->getWhereClause());
        $from_string            = 'FROM ' . $this->formatFromList($statement->getFromSources());
    
        $select_stmt_string = "SELECT {$select_expression_list}\n{$from_string}\n{$where_string}";
        
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
            if (is_string($item)) $expression_list[] = $this->formatterFactory->format($this->formatterFactory->proxy($item, ColumnFormattingProxy::class));
            else throw new UnimplementedError("+ anything but strings in the expression list");
        }
        return join(", ", $expression_list);
    }
    /**
     * What we will put the values in
     *
     * @param $source_array
     *
     * @return string
     */
    protected function formatFromList($source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) {
            $formatter = $this->formatterFactory;
            $source    = $this->formatterFactory->format($formatter->proxy($source,
                                                                           TableFormattingProxy::class));
            $sources[] = $source;
        }
        return join(', ', $sources);
    }
}