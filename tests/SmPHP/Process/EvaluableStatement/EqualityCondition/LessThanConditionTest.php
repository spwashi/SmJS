<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 3:28 PM
 */

namespace Sm\Process\EvaluableStatement\EqualityCondition;


class LessThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $EvaluableStatement = LessThanCondition::init(2, 3);
        $this->assertTrue($EvaluableStatement->resolve());
        
        $EvaluableStatement = LessThanCondition::init(3, 2);
        $this->assertFalse($EvaluableStatement->resolve());
    }
}
