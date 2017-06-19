<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 3:28 PM
 */

namespace Sm\Process\EvaluableStatement\EqualityCondition;


class GreaterThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $EvaluableStatement = GreaterThanCondition::init(3, 2);
        $this->assertTrue($EvaluableStatement->resolve());
        
        $EvaluableStatement = GreaterThanCondition::init(2, 3);
        $this->assertFalse($EvaluableStatement->resolve());
    }
}
