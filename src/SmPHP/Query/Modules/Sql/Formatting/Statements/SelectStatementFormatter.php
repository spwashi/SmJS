<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 9:45 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing\TableAliasProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableReferenceFormattingProxy;
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
        $where_string           = $this->queryFormatter->format($statement->getWhereClause());
        $sources                = $statement->getFromSources();
    
        foreach ($sources as $source) {
            /** @var \Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableFormattingProxy $tableProxy */
            $tableProxy = $this->proxy($source, TableReferenceFormattingProxy::class);
            $aliasName  = str_shuffle($tableProxy->getTableName());
            $this->queryFormatter->getAliasContainer()->register($source, new TableAliasProxy($tableProxy, $aliasName));
        }
        $from_string = 'FROM ' . $this->formatSourceList($sources);
        
        $select_stmt_string = "SELECT {$select_expression_list}\n{$from_string}\n{$where_string}";
        
        return $select_stmt_string;
    }
    protected function formatSourceList($source_array): string {
        $sources = [];
        if (!isset($this->queryFormatter)) throw new IncompleteFormatterException("No formatter Factory");
        foreach ($source_array as $index => $source) {
            $formatter = $this->queryFormatter;
            $alias     = $this->queryFormatter->getAliasContainer()->getFinalAlias($source);
            if ($alias !== $source) {
                $alias_proxy = $this->proxy($alias, TableReferenceFormattingProxy::class);
                $proxy       = $this->proxy($source, TableReferenceFormattingProxy::class);
                $source      = $this->queryFormatter->format($proxy) . ' AS ' . $this->queryFormatter->format($alias_proxy);
            }
            $sources[] = $source;
        }
        return join(', ', $sources);
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
            $formatter         = $this->queryFormatter;
            $expression_list[] = $formatter->format($formatter->proxy($item, ColumnIdentifierFormattingProxy::class));
        }
        return join(", ", $expression_list);
    }
}