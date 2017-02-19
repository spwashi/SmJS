<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 3:28 PM
 */

namespace Sm\Condition\EqualityCondition;


class LessThanConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $Condition = LessThanCondition::init(2, 3);
        $this->assertTrue($Condition->resolve());
        
        $Condition = LessThanCondition::init(3, 2);
        $this->assertFalse($Condition->resolve());
    }
}
