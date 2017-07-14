<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Query\Modules\Sql\Formatting\Clauses\WhereClauseFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\InsertStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\UpdateStatementFormatter;
use Sm\Query\Statements\Clauses\WhereClause;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

class SqlQueryFormatterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter $queryFormatter */
    public $queryFormatter;
    public $formatterFactory;
    public function setUp() {
        $formatterFactory     = $this->formatterFactory = new SqlQueryFormatterFactory;
        $this->queryFormatter = new SqlQueryFormatter($this->formatterFactory);
        # Default
        $this->formatterFactory->register(null, new StdSqlFormatter);
        $this->formatterFactory->register(SelectStatement::class, new SelectStatementFormatter($formatterFactory));
        $this->formatterFactory->register(UpdateStatement::class, new UpdateStatementFormatter($formatterFactory));
        $this->formatterFactory->register(InsertStatement::class, new InsertStatementFormatter($formatterFactory));
        $this->formatterFactory->register(TwoOperandStatement::class,
                                          $formatterFactory->createFormatter(function (TwoOperandStatement $stmt) use ($formatterFactory) {
                                              $left     = $stmt->getLeftSide();
                                              $operator = $stmt->getOperator();
                                              $right    = $stmt->getRightSide();
                                              return $formatterFactory($left) . ' ' . $operator . ' ' . $formatterFactory($right);
                                          }));
        $this->formatterFactory->register(WhereClause::class,
                                          new WhereClauseFormatter($formatterFactory));
    }
    
    
    public function testSelect() {
        $stmt   = SelectStatement::init('column_1', 'column_2')
                                 ->from('here', 'there')
                                 ->where(EqualToCondition::init(1, 2));
        $result = $this->queryFormatter->format($stmt);
        var_dump($result);
    }
    public function testUpdate() {
        $stmt   = UpdateStatement::init([ 'test1' => 'test2' ])
                                 ->inSources('testHELP');
        $result = $this->queryFormatter->format($stmt);
        var_dump($result);
    }
    
    public function testInsert() {
        $stmt   = InsertStatement::init()
                                 ->set([ 'title' => 'hello', 'first_name' => 'last_name' ],
                                       [ 'title' => 'hey there', 'first_name' => 'another' ])
                                 ->inSources('tbl');
        $result = $this->queryFormatter->format($stmt);
        var_dump($result);
    }
}
