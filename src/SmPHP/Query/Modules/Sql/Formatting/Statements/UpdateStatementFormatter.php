<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:40 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
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
    use MightFormatSourceListTrait;
    
    public function format($statement): string {
        if (!($statement instanceof UpdateStatement)) throw new InvalidArgumentException("Can only format UpdateStatements");
        
        $update_expression_list = $this->formatUpdateExpressionList($statement->getUpdatedItems());
        $where_string           = $this->formatterFactory->format($statement->getWhereClause());
        $source_string          = $this->formatSourceList($statement->getIntoSources());
    
        $update_stmt = "UPDATE {$source_string} \nSET\t{$update_expression_list}\n{$where_string}";
        
        $update_stmt = trim($update_stmt);
        
        return $update_stmt;
    }
    protected function formatUpdateExpressionList(array $updates) {
        $expression_list = [];
        foreach ($updates as $item) {
            if (is_array($item)) {
                foreach ($item as $key => $value) {
                    $formatter         = $this->formatterFactory;
                    $key               = $this->formatterFactory->format($formatter->proxy($key,
                                                                                           ColumnIdentifierFormattingProxy::class));
                    $value             = $this->formatterFactory->format($value);
                    $expression_list[] = "{$key} = {$value}";
                }
            } else throw new UnimplementedError("+ Anything but associative in the expression list");
        }
        return join(",\n\t", $expression_list);
    }
}