<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Util;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\DatabaseDataSource;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;
use Sm\Query\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Query\Modules\Sql\Data\Column\VarcharColumnSchema;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Query\Modules\Sql\Formatting\Clauses\ConditionalClauseFormatter;
use Sm\Query\Modules\Sql\Formatting\Column\ColumnSchemaFormatter;
use Sm\Query\Modules\Sql\Formatting\Column\IntegerColumnSchemaFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\ColumnIdentifierFormattingProxyFormatter;
use Sm\Query\Modules\Sql\Formatting\Component\SelectExpressionFormattingProxyFormatter;
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
use Sm\Query\Modules\Sql\Formatting\Statements\InsertStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\Table\CreateTableStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\UpdateStatementFormatter;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\SqlExecutionContext;
use Sm\Query\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Statements\Clauses\ConditionalClause;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

class SqlQueryFormatterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter $queryFormatter */
    public $queryFormatter;
    public $formattingProxyFactory;
    public function setUp() {
        $formattingContext = SqlExecutionContext::init();
        #--- ProxyFactory
        $formattingProxyFactory = new SqlFormattingProxyFactory;
        $formattingProxyFactory->register([ ColumnIdentifierFormattingProxy::class                  => function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
            if ($item instanceof ColumnSchema) {
                return $formattingProxyFactory->build(ColumnSchema_ColumnIdentifierFormattingProxy::class, $item);
            }
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
                                            }, ]);
        #--- FormatterFactory
        $formatterFactory = SqlQueryFormatterFactory::init($formattingProxyFactory, SqlFormattingAliasContainer::init(), $formattingContext);
        $formatterFactory->register(
            [
                new StdSqlFormatter,
                SelectStatement::class                 => new SelectStatementFormatter($formatterFactory),
                UpdateStatement::class                 => new UpdateStatementFormatter($formatterFactory),
                CreateTableStatement::class            => new CreateTableStatementFormatter($formatterFactory),
                InsertStatement::class                 => new InsertStatementFormatter($formatterFactory),
                ConditionalClause::class               => new ConditionalClauseFormatter($formatterFactory),
                ColumnSchema::class                    => new ColumnSchemaFormatter($formatterFactory),
                IntegerColumnSchema::class             => new IntegerColumnSchemaFormatter($formatterFactory),
                ColumnIdentifierFormattingProxy::class => new ColumnIdentifierFormattingProxyFormatter($formatterFactory),
                TwoOperandStatement::class             => new TwoOperandStatementFormatter($formatterFactory),
                PlaceholderFormattingProxy::class      =>
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
        #--- setup
        $this->queryFormatter = new SqlQueryFormatter($formatterFactory);
    }
    
    
    public function testSelect() {
        $tableSource   = new TableSource(new DatabaseDataSource(new MySqlAuthentication, 'Database'), 'tablename_is_here');
        $tableSource_2 = new TableSource(new DatabaseDataSource(new MySqlAuthentication, 'Database'), 'another_table');
        $boonman       = VarcharColumnSchema::init('boonman')
                                            ->setLength(25)
                                            ->setTableSchema($tableSource);
        $bran_slam     = VarcharColumnSchema::init('bran_slam')
                                            ->setLength(25)
                                            ->setTableSchema($tableSource);
        $stmt          = SelectStatement::init('here.column_1', $boonman, $bran_slam, 'column_2')
                                        ->from('there', JoinedSourceSchematic::init()
                                                                             ->setOriginSources($tableSource)
                                                                             ->setJoinConditions(EqualToCondition::init(1, 2))
                                                                             ->setJoinedSources($tableSource_2))
                                        ->where(EqualToCondition::init(1, $bran_slam));
        $result        = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
    public function testUpdate() {
        $tableSource = new TableSource(new DatabaseDataSource(new MySqlAuthentication, 'Database'), 'tablename_is_here');
        $boonman     = VarcharColumnSchema::init('boonman')
                                          ->setLength(25)
                                          ->setTableSchema($tableSource);
        $stmt        = UpdateStatement::init([ 'test1' => 'test2', 'test4' => 'test5', 'test7' => 13.5 ])
                                      ->inSources('testHELP');
        $result      = $this->queryFormatter->format($stmt);
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
        $vcColumn1 = VarcharColumnSchema::init('column_name')
                                        ->setNullability(0)
                                        ->setLength(255);
        $iColumn1  = IntegerColumnSchema::init('one_thing')
                                        ->setAutoIncrement()
                                        ->setLength(10);
        $vcColumn2 = VarcharColumnSchema::init()
                                        ->setLength(255)
                                        ->setName('this_thing');
        $vcColumn3 = VarcharColumnSchema::init('boon_man')
                                        ->setLength(255);
    
        $primaryKey = PrimaryKeyConstraintSchema::init()
                                                ->addColumn($vcColumn1)
                                                ->addColumn($iColumn1);
        $stmt       = CreateTableStatement::init('TableName')
                                          ->withColumns($vcColumn1,
                                                        $iColumn1,
                                                        $vcColumn2,
                                                        $vcColumn3)
                                          ->withConstraints($primaryKey);
        
        $result = $this->queryFormatter->format($stmt);
        echo __FILE__ . "\n--\n$result\n\n";
    }
}
