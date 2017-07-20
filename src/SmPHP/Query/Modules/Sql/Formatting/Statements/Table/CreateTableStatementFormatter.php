<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 7:30 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Constraints\KeyConstraintSchema;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Modules\Sql\Type\Column\ColumnSchema;

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
        $table_name                     = $statement->getName();
        $columns                        = $statement->getColumns();
        $constraints                    = $statement->getConstraints();
        $formattedColumnsAndConstraints = [];
        foreach ($columns as $column) {
            if (!($column instanceof ColumnSchema)) throw new InvalidArgumentException("Can only create tables with column schemas");
            $formattedColumnsAndConstraints[] = $this->queryFormatter->format($column);
        }
        foreach ($constraints as $constraint) {
            if (!($constraint instanceof KeyConstraintSchema)) throw new InvalidArgumentException("Can only create tables with KeyConstraints");
            $formattedColumnsAndConstraints[] = $this->queryFormatter->format($constraint);
        }
    
    
        $f_c_string = join(",\n\t", $formattedColumnsAndConstraints);
        return "CREATE TABLE {$table_name}(\n\t{$f_c_string}\n)";
    }
    
}