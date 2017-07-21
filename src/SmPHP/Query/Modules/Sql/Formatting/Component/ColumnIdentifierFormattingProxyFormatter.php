<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 7:33 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Component;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableReferenceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;

class ColumnIdentifierFormattingProxyFormatter extends SqlQueryFormatter {
    /**
     * Format the String_ColumnIdentifierFormattingProxy
     *
     * @param \Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy $columnFormattingProxy
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function format($columnFormattingProxy): string {
        if (!($columnFormattingProxy instanceof ColumnIdentifierFormattingProxy)) {
            throw new InvalidArgumentException("Can only format String_ColumnIdentifierFormattingProxies");
        }
        
        $column_name = '`' . $columnFormattingProxy->getColumnName() . '`';
        $table       = $columnFormattingProxy->getTable();
        if ($table) {
            # todo replace with local Alias Container in String_ColumnIdentifierFormattingProxy class someday
            $tableProxy            = $this->proxy($table, TableReferenceFormattingProxy::class);
            $aliasedTableProxy     = $this->getFinalAlias($tableProxy);
            $aliasAsTableReference = $this->proxy($aliasedTableProxy, TableReferenceFormattingProxy::class);
            $column_name           = $this->formatComponent($aliasAsTableReference) . '.' . $column_name;
        }
        return $column_name;
    }
    
}