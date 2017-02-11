<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 2:42 PM
 */

namespace Sm\Condition\Equality;


use Sm\Condition\EqualityCondition\EqualToCondition;


class EqualToConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $Condition = EqualToCondition::init(2, 2);
        $this->assertEquals(true, $Condition->resolve());
    }
}
