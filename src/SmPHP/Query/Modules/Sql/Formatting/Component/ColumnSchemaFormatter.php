<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 11:03 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;

class ColumnSchemaFormatter extends SqlQueryFormatter {
    public function format($columnSchema): string {
        if (!($columnSchema instanceof ColumnSchema)) {
            throw new InvalidArgumentException("Can only format String_ColumnIdentifierFormattingProxies");
        }
        $column_name = $columnSchema->getName();
        $type        = $columnSchema->getType();
        $unique      = $columnSchema->isUnique() ? 'UNIQUE' : '';
        $can_be_null = $columnSchema->canBeNull() ? 'NULL' : 'NOT NULL';
        $length      = $columnSchema->getLength();
        $length      = $length ? "($length)" : '';
        return "{$column_name} {$can_be_null} {$type} {$length} {$unique}";
    }
    
}