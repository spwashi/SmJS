<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 1:08 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\InsertStatement;

class InsertStatementFormatter extends SqlQueryFormatter {
    public function format($statement): string {
        if (!($statement instanceof InsertStatement)) throw new InvalidArgumentException("Can only format InsertStatements");
        
        list($column_string, $insertExpressionList) = $this->formatInsertExpressionList($statement->getInsertedItems());
        $source_string = $this->formatSourceList($statement->getIntoSources());
        
        
        $update_stmt = "INSERT INTO {$source_string} ({$column_string}) VALUES {$insertExpressionList}";
        
        return $update_stmt;
    }
    /**
     * What we will put the values in
     *
     * @param $source_array
     *
     * @return string
     */
    protected function formatSourceList(array $source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) $sources[] = $source;
        return join(', ', $sources);
    }
    protected function formatInsertExpressionList(array $inserted_items): array {
        $columns = [];
        foreach ($inserted_items as $number => $insert_collection) {
            if (!is_array($insert_collection)) throw new InvalidArgumentException("Trying to insert a non-array (index {$number})");
            foreach ($insert_collection as $column_name => $value) {
                if (is_numeric($column_name)) throw new InvalidArgumentException("Trying to insert a non-associative array (index {$column_name} in {$number})");
                $columns[ $column_name ] = null;
            }
        }
        # todo Sets in PHP?
        $columns      = array_keys($columns);
        $insert_array = [];
        foreach ($inserted_items as $index => $inserted_item) {
            $_insert_arr = [];
            foreach ($columns as $column) {
                if (array_key_exists($column, $inserted_item)) {
                    $_insert_arr[ $column ] = $this->formatterFactory->format($inserted_item[ $column ]);
                } else {
                    $_insert_arr[ $column ] = 'DEFAULT';
                }
            }
            $insert_array[] = '(' . join(', ', $_insert_arr) . ')';
        }
        return [ join(', ', $columns), join(', ', $insert_array) ];
    }
}