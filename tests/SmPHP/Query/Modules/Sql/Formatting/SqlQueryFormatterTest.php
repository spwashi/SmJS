<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Data\Modules\Sql\Type\Column\ColumnSchema;
use Sm\Data\Modules\Sql\Type\Column\VarcharColumnSchema;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Query\Modules\Sql\Formatting\Clauses\WhereClauseFormatter;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Column\String_ColumnIdentifierFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Database\String_DatabaseFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\String_TableReferenceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableNameFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableReferenceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Statements\InsertStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\Table\CreateTableStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\UpdateStatementFormatter;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Statements\Clauses\WhereClause;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;
use Sm\Storage\Database\DatabaseDataSource;
use Sm\Storage\Database\TableSource;

class SqlQueryFormatterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter $queryFormatter */
    public $queryFormatter;
    public $formatterFactory;
    public $formattingProxyFactory;
    public function setUp() {
        $this->formattingProxyFactory = $formattingProxyFactory = new SqlFormattingProxyFactory;
        $this->formattingProxyFactory->register(ColumnIdentifierFormattingProxy::class, function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
            if (is_string($item)) return $formattingProxyFactory->build(String_ColumnIdentifierFormattingProxy::class, $item);
            throw new UnimplementedError('+ Anything but strings');
        });
        $this->formattingProxyFactory->register(String_ColumnIdentifierFormattingProxy::class, String_ColumnIdentifierFormattingProxy::class);
        $this->formattingProxyFactory->register(String_TableReferenceFormattingProxy::class, String_TableReferenceFormattingProxy::class);
        $this->formattingProxyFactory->register(TableReferenceFormattingProxy::class, function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
            if ($item instanceof TableSource) {
                $database = $item->getParentSource();
                $name     = $item->getName();
                
                if ($database && $database->getName()) {
                    $name = $database->getName() . '.' . $name;
                }
                $item = $name;
            } else if ($item instanceof TableFormattingProxy) {
                $item = $item->getTableName();
            }
            
            # Default to formatting tables as strings
            if (is_string($item)) return $formattingProxyFactory->build(String_TableReferenceFormattingProxy::class, $item);
            throw new UnimplementedError('+ Anything but strings');
        });
        $this->formatterFactory = $formatterFactory = new SqlQueryFormatterFactory($formattingProxyFactory, SqlFormattingAliasContainer::init());
        $this->queryFormatter   = new SqlQueryFormatter($this->formatterFactory);
        
        # Default
        $this->formatterFactory->register(null, new StdSqlFormatter);
        $this->formatterFactory->register(SelectStatement::class, new SelectStatementFormatter($formatterFactory));
        $this->formatterFactory->register(UpdateStatement::class, new UpdateStatementFormatter($formatterFactory));
        $this->formatterFactory->register(CreateTableStatement::class, new CreateTableStatementFormatter($formatterFactory));
        $this->formatterFactory->register(InsertStatement::class, new InsertStatementFormatter($formatterFactory));
        $this->formatterFactory->register(WhereClause::class, new WhereClauseFormatter($formatterFactory));
        #
        $this->formatterFactory->register(ColumnSchema::class,
                                          $formatterFactory->createFormatter(function (ColumnSchema $columnSchema) use ($formatterFactory) {
                                              $column_name = $columnSchema->getName();
                                              $type        = $columnSchema->getType();
                                              $can_be_null = $columnSchema->canBeNull() ? 'NULL' : 'NOT NULL';
                                              $length      = $columnSchema->getLength();
                                              $length      = $length ? "($length)" : '';
                                              return "{$column_name} {$can_be_null} {$type} {$length}";
                                          }));
        $this->formatterFactory->register(String_ColumnIdentifierFormattingProxy::class,
                                          $formatterFactory->createFormatter(function (String_ColumnIdentifierFormattingProxy $columnFormattingProxy) use ($formatterFactory) {
                                              $column_name = '`' . $columnFormattingProxy->getColumnName() . '`';
    
                                              $tableProxy = $columnFormattingProxy->getTable();
                                              if ($tableProxy) {
                                                  # todo replace with local Alias Container in String_ColumnIdentifierFormattingProxy class someday
                                                  $aliasedTableProxy = $formatterFactory->getAliasContainer()->getFinalAlias($tableProxy);
                                                  $column_name       = $formatterFactory->format($aliasedTableProxy) . '.' . $column_name;
                                              }
                                              return $column_name;
                                          }));
        $this->formatterFactory->register(String_DatabaseFormattingProxy::class,
                                          $formatterFactory->createFormatter(function (String_DatabaseFormattingProxy $columnFormattingProxy) use ($formatterFactory) {
                                              $formatted_database = '`' . $columnFormattingProxy->getDatabaseName() . '`';
                                              return $formatted_database;
                                          }));
        $this->formatterFactory->register(TableNameFormattingProxy::class,
                                          $formatterFactory->createFormatter(function (TableNameFormattingProxy $tableNameFormattingProxy) {
                                              return '`' . $tableNameFormattingProxy->getTableName() . '`';
                                          }));
        $this->formatterFactory->register(String_TableReferenceFormattingProxy::class,
                                          $formatterFactory->createFormatter(function (String_TableReferenceFormattingProxy $columnFormattingProxy) use ($formatterFactory) {
                                              $formatted_table = '`' . $columnFormattingProxy->getTableName() . '`';
                                              if ($columnFormattingProxy->getDatabase()) {
                                                  $formatted_table = $formatterFactory->format($columnFormattingProxy->getDatabase()) . '.' . $formatted_table;
                                              }
                                              return $formatted_table;
                                          }));
        $this->formatterFactory->register(TwoOperandStatement::class,
                                          $formatterFactory->createFormatter(function (TwoOperandStatement $stmt) use ($formatterFactory) {
                                              $left     = $stmt->getLeftSide();
                                              $operator = $stmt->getOperator();
                                              $right    = $stmt->getRightSide();
                                              return $formatterFactory($left) . ' ' . $operator . ' ' . $formatterFactory($right);
                                          }));
    }
    
    
    public function testSelect() {
        $tableSource = new TableSource(new DatabaseDataSource(new MySqlAuthentication, 'Database'), 'tablename_is_here');
        $stmt        = SelectStatement::init('here.column_1', 'column_2')
                                      ->from($tableSource, 'there')
                                      ->where(EqualToCondition::init(1, 2));
        $result      = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testUpdate() {
        $stmt   = UpdateStatement::init([ 'test1' => 'test2', 'test4' => 'test5', 'test7' => 13.5 ])
                                 ->inSources('testHELP');
        $result = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testInsert() {
        $stmt   = InsertStatement::init()
                                 ->set([ 'title' => 'hello', 'first_name' => 'last_name' ],
                                       [ 'title' => 'hey there', 'first_name' => 'another' ])
                                 ->inSources('tbl');
        $result = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testCreateTable() {
        $stmt   = CreateTableStatement::init('TableName')
                                      ->withColumns(VarcharColumnSchema::init('column_name')->setLength(255));
        $result = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
}
