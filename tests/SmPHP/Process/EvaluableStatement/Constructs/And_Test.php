<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 5:31 PM
 */

namespace Sm\Process\EvaluableStatement\Constructs;


class And_Test extends \PHPUnit_Framework_TestCase {
    public function testCanCreateAnd() {
        $this->assertTrue(And_::init()->set(true, 1, "-")->resolve());
        $this->assertFalse(And_::init()->set(true, null)->resolve());
        $this->assertFalse(And_::init()->set(true, false)->resolve());
        $this->assertFalse(And_::init()->set(true, "")->resolve());
        $this->assertFalse(And_::init()->set(true, 0)->resolve());
    }
}
