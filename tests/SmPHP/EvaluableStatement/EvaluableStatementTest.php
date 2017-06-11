<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:57 AM
 */

namespace Sm\EvaluableStatement;


class EvaluableStatementTest extends \PHPUnit_Framework_TestCase {
    /** @var  $EvaluableStatement EvaluableStatement */
    protected $EvaluableStatement;
    public function setUp() {
        $this->EvaluableStatement
            = $EvaluableStatement
            = $this->getMockForAbstractClass(EvaluableStatement::class);
    }
    public function testCanRegisterEvaluators() {
        $evaluators = [
            function () { echo '0'; },
            function () { return 'val'; },
            function () { echo '1'; },
            function () { echo '2'; },
            function () { echo '3'; },
        ];
        $this->expectOutputString('321');
        $this->EvaluableStatement->register($evaluators)->resolve();
    }
    public function testCanCreate() {
        $EvaluableStatement = $this->EvaluableStatement;
        $this->assertInstanceOf(EvaluableStatement::class, $EvaluableStatement);
    }
    public function testCanTellIfResolvesToValue() {
        $this->EvaluableStatement;
    }
}
