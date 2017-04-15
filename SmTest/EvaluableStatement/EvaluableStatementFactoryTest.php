<?php
/**
 * User: Sam Washington
 * Date: 2/26/17
 * Time: 4:37 PM
 */

namespace Sm\EvaluableStatement;


class EvaluableStatementFactoryTest extends \PHPUnit_Framework_TestCase {
    /** @var  EvaluableStatementFactory $Factory */
    protected $Factory;
    public function setUp() {
        $this->Factory = new EvaluableStatementFactory;
    }
    public function testCanGetAncestorClasses() {
        $Factory = $this->Factory;
        # Callback to return a mock EvaluableStatement
        $fn_get_class = function () {
            $Mock = $this->getMockForAbstractClass(EvaluableStatement::class);
            $Mock->method('getDefaultEvaluator')->willReturn(function () { return 'test.return'; });
            return $Mock;
        };
        
        
        # Register that class under the pseudonym "test"
        $Factory->register('test', $fn_get_class);
        
        # For "test", register two callbacks. The last to be registered should be the first to be called.
        $Factory->registerEvaluatorForClass('test', function () { echo '|test.echo.2'; });
        $Factory->registerEvaluatorForClass('test', function () { echo 'test.echo.1'; });
        
        /** @var EvaluableStatement $class */
        $class  = $Factory->build('test');
        $result = $class->resolve();
        $this->assertEquals('test.return', $result);
        
        
        $this->expectOutputString('test.echo.1|test.echo.2');
    }
}
