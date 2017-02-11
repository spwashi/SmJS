<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 3:28 PM
 */

namespace Sm\Condition\EqualityCondition;


class GreaterThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $Condition = GreaterThanCondition::init(3, 2);
        $this->assertTrue($Condition->resolve());
        
        $Condition = GreaterThanCondition::init(2, 3);
        $this->assertFalse($Condition->resolve());
    }
}
