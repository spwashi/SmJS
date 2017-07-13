<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\PlainStringFormatter;
use Sm\Data\Evaluation\Comparison\EqualToCondition;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Query\Modules\Sql\Formatting\Clauses\WhereClauseFormatter;
use Sm\Query\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Query\Statements\Clauses\WhereClause;
use Sm\Query\Statements\SelectStatement;

class SqlQueryFormatterTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter $queryFormatter */
    public $queryFormatter;
    public $formatterFactory;
    public function setUp() {
        $formatterFactory     = $this->formatterFactory = new SqlQueryFormatterFactory;
        $this->queryFormatter = new SqlQueryFormatter($this->formatterFactory);
        # Default
        $this->formatterFactory->register(null, new PlainStringFormatter);
        $this->formatterFactory->register(SelectStatement::class, new SelectStatementFormatter($formatterFactory));
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
        $selectStatement = SelectStatement::init('column_1', 'column_2')
                                          ->from('here', 'there')
                                          ->where(EqualToCondition::init(1, 2));
        
        $result = $this->queryFormatter->format($selectStatement);
        var_dump($result);
    }
}
