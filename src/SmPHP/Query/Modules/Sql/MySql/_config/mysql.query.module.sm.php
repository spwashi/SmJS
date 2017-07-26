<?php
/**
 * 'g.'User: Sam Washington
 * Date: 7/24/17
 * Time: 8:51 PM
 **/

use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\Util;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;
use Sm\Query\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Query\Modules\Sql\Formatting\Clauses\ConditionalClauseFormatter;
use Sm\Query\Modules\Sql\Formatting\Column\ColumnSchemaFormatter;
use Sm\Query\Modules\Sql\Formatting\Column\IntegerColumnSchemaFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\ColumnIdentifierFormattingProxyFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\SelectExpressionFormattingProxyFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\String_ColumnIdentifierFormattingProxyFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\TwoOperandStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnSchema_ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\String_ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Component\SelectExpressionFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\PlaceholderFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\String_TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableSourceSchema_TableIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Source\JoinedSourceSchemaFormatter;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory;
use Sm\Query\Modules\Sql\Formatting\Statements\InsertStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\Table\CreateTableStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\UpdateStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\StdSqlFormatter;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Proxy\String_QueryProxy;
use Sm\Query\Statements\Clauses\ConditionalClause;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

function register_proxy_handlers(SqlFormattingProxyFactory $formattingProxyFactory) {
    $formattingProxyFactory->register([
                                          ColumnIdentifierFormattingProxy::class                  => function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
                                              if ($item instanceof ColumnSchema) {
                                                  return $formattingProxyFactory->build(ColumnSchema_ColumnIdentifierFormattingProxy::class, $item);
                                              }
            
                                              if ($item instanceof StringResolvable) $item = "$item";
            
                                              if (is_string($item)) {
                                                  return $formattingProxyFactory->build(String_ColumnIdentifierFormattingProxy::class, $item);
                                              }
                                              throw new UnimplementedError('+ Anything but strings [' . Util::getShapeOfItem($item) . ']');
                                          },
                                          ColumnSchema_ColumnIdentifierFormattingProxy::class     => ColumnSchema_ColumnIdentifierFormattingProxy::class,
                                          String_ColumnIdentifierFormattingProxy::class           => String_ColumnIdentifierFormattingProxy::class,
                                          String_TableIdentifierFormattingProxy::class            => String_TableIdentifierFormattingProxy::class,
                                          TableSourceSchema_TableIdentifierFormattingProxy::class => TableSourceSchema_TableIdentifierFormattingProxy::class,
                                          TableIdentifierFormattingProxy::class                   => function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
                                              if ($item instanceof TableSource) {
                                                  return $formattingProxyFactory->build(TableSourceSchema_TableIdentifierFormattingProxy::class, $item);
                                              }
            
            
                                              if ($item instanceof NamedDataSourceSchema) $item = $item->getName();
            
                                              # Default to formatting tables as strings
                                              if (is_string($item)) {
                                                  return $formattingProxyFactory->build(String_TableIdentifierFormattingProxy::class, $item);
                                              }
            
                                              throw new UnimplementedError('+ Anything but strings[' . Util::getShapeOfItem($item) . ']');
                                          },
                                      ]);
}

function register_formatting_handlers(SqlQueryFormatterFactory $formatterFactory) {
    $formatterFactory->register(
        [
            new StdSqlFormatter,
            String_QueryProxy::class                      => $formatterFactory->createFormatter(function (String_QueryProxy $proxy) {
                return $proxy->getQuery();
            }),
            StringResolvable::class                       => $formatterFactory->createFormatter(function (StringResolvable $stringResolvable) {
                return '"' . $stringResolvable . '"';
            }),
            SelectStatement::class                        => new SelectStatementFormatter($formatterFactory),
            UpdateStatement::class                        => new UpdateStatementFormatter($formatterFactory),
            CreateTableStatement::class                   => new CreateTableStatementFormatter($formatterFactory),
            InsertStatement::class                        => new InsertStatementFormatter($formatterFactory),
            ConditionalClause::class                      => new ConditionalClauseFormatter($formatterFactory),
            ColumnSchema::class                           => new ColumnSchemaFormatter($formatterFactory),
            IntegerColumnSchema::class                    => new IntegerColumnSchemaFormatter($formatterFactory),
            ColumnIdentifierFormattingProxy::class        => new ColumnIdentifierFormattingProxyFormatter($formatterFactory),
            String_ColumnIdentifierFormattingProxy::class => new String_ColumnIdentifierFormattingProxyFormatter($formatterFactory),
            TwoOperandStatement::class                    => new TwoOperandStatementFormatter($formatterFactory),
            PlaceholderFormattingProxy::class             =>
                $formatterFactory->createFormatter(function (PlaceholderFormattingProxy $columnSchema) use ($formatterFactory) {
                    return ":{$columnSchema->getPlaceholderName()}";
                }),
            
            JoinedSourceSchematic::class           => new JoinedSourceSchemaFormatter($formatterFactory),
            SelectExpressionFormattingProxy::class => new SelectExpressionFormattingProxyFormatter($formatterFactory),
            PrimaryKeyConstraintSchema::class      =>
                $formatterFactory->createFormatter(function (PrimaryKeyConstraintSchema $primaryKeyConstraintSchema) use ($formatterFactory) {
                    $columns      = $primaryKeyConstraintSchema->getColumns();
                    $column_names = [];
                    foreach ($columns as $column) {
                        $column_names[] = $column->getName();
                    }
                    $column_name_string = join(', ', $column_names);
                    return "PRIMARY KEY({$column_name_string})";
                }),
            NamedDataSourceFormattingProxy::class  =>
                $formatterFactory->createFormatter(function (NamedDataSourceFormattingProxy $tableNameFormattingProxy) {
                    return '`' . $tableNameFormattingProxy->getName() . '`';
                }),
            TableIdentifierFormattingProxy::class  =>
                $formatterFactory->createFormatter(function (TableIdentifierFormattingProxy $columnFormattingProxy) use ($formatterFactory) {
                    $formatted_table = '`' . $columnFormattingProxy->getName() . '`';
                    return $formatted_table;
                }),
        ]);
}