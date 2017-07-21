<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 9:45 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing\AliasedTableFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableReferenceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Statements\SelectStatement;

class SelectStatementFormatter extends SqlQueryFormatter implements Formatter {
    /**
     * Return the item Formatted in the specific way
     *
     * @param SelectStatement $columnSchema
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function format($columnSchema): string {
        if (!($columnSchema instanceof SelectStatement)) {
            throw new InvalidArgumentException("Can only format SelectStatements");
        }
        
        $selects = $columnSchema->getSelectedItems();
        $sources = $columnSchema->getFromSources();
        $where   = $columnSchema->getWhereClause();
        $this->aliasSources($sources);
        
        $select_expression_list = $this->formatSelectExpressionList($selects);
        $where_string           = $this->formatComponent($where);
        $from_string            = $this->formatSelectList($sources);
        $select_stmt_string     = "SELECT\t{$select_expression_list}\nFROM\t{$from_string}\n{$where_string}";
        
        return $select_stmt_string;
    }
    public function aliasSources(array $sources) {
        foreach ($sources as $source) {
            # Don't alias strings
            if (is_string($source)) continue;
            # ALIAS THE TABLE
            $tableProxy = $this->proxy($source, TableReferenceFormattingProxy::class);
            $this->alias($tableProxy, AliasedTableFormattingProxy::class);
        }
        return $sources;
    }
    /**
     * Formate the things that will be in the "select list"
     *
     * @param $source_array
     *
     * @return string
     */
    protected function formatSelectList($source_array): string {
        $sources = [];
        foreach ($source_array as $index => $source) {
            $sourceProxy      = $this->proxy($source, TableReferenceFormattingProxy::class);
            $formatted_source = $this->formatComponent($sourceProxy);
    
    
            # If the alias is the same as the proxy, we haven't aliased it (so no need to do the AS)
            $alias = $this->getFinalAlias($sourceProxy);
            if ($alias !== $sourceProxy) {
                $aliasProxy       = $this->proxy($alias, TableReferenceFormattingProxy::class);
                $formatted_source .= ' AS ' . $this->formatComponent($aliasProxy);
            }
    
    
            $sources[] = $formatted_source;
        }
        return join(",\n\t\t", $sources);
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
            # Assume it's a column - otherwise, we'd use a different object
            $proxy             = $this->queryFormatter->proxy($item, ColumnIdentifierFormattingProxy::class);
            $expression_list[] = $this->formatComponent($proxy);
        }
        return join(",\n\t\t", $expression_list);
    }
}