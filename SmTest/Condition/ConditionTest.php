<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:57 AM
 */

namespace Sm\Test\Condition;


use Sm\Condition\Condition;

class ConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Condition = new Condition;
        $this->assertInstanceOf(Condition::class, $Condition);
        return $Condition;
    }
    public function testCanResolve() {
        $Condition = new Condition;
        $this->assertNull($Condition->resolve());
    }
}
