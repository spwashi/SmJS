<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 7:30 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Modules\Sql\Type\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;

/**
 * Class CreateTableStatementFormatter
 *
 * Meant to format statements to create a table
 *
 * @package Sm\Query\Modules\Sql\Formatting\Statements\Table
 */
class CreateTableStatementFormatter extends SqlQueryFormatter {
    /**
     * @param $statement
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($statement): string {
        if (!($statement instanceof CreateTableStatement)) throw new UnimplementedError("+ Anything but CreateTableStatements");
        $table_name       = $statement->getName();
        $columns          = $statement->getColumns();
        $formattedColumns = [];
        foreach ($columns as $column) {
            if (!($column instanceof ColumnSchema)) throw new InvalidArgumentException("Can only create tables with column schemas");
            $formattedColumns[] = $this->formatterFactory->format($column);
        }
        $f_c_string = join(",\n\t", $formattedColumns);
        return "CREATE TABLE {$table_name}(\n\t{$f_c_string}\n)";
    }
    
}