<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:40 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableReferenceFormattingProxy;
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
    
        $update_stmt = "UPDATE {$source_string} \nSET\t{$update_expression_list}\n{$where_string}";
        
        $update_stmt = trim($update_stmt);
        
        return $update_stmt;
    }
    /**
     * Format the list of sources as they are going to be used in the UPDATE statement
     *
     * @param $source_array
     *
     * @return string
     * @throws \Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException
     */
    protected function formatSourceList($source_array): string {
        $sources = [];
        if (!isset($this->formatterFactory)) throw new IncompleteFormatterException("No formatter Factory");
        foreach ($source_array as $index => $source) {
            $sources[] = $this->formatterFactory->format($this->proxy($source, TableReferenceFormattingProxy::class));
        }
        return join(', ', $sources);
    }
    protected function formatUpdateExpressionList(array $updates) {
        $expression_list = [];
        foreach ($updates as $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $key   = $this->formatterFactory->format($this->proxy($key, ColumnIdentifierFormattingProxy::class));
                    $value = $this->formatterFactory->format($value);
                    
                    $expression_list[] = "{$key} = {$value}";
                }
            } else throw new UnimplementedError("+ Anything but associative in the expression list");
        }
        return join(",\n\t", $expression_list);
    }
}