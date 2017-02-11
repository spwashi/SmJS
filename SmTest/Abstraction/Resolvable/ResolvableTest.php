<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 8:58 PM
 */

namespace Sm\Test\Abstraction\Resolvable;


use Sm\Abstraction\Resolvable\Resolvable;

class ResolvableTest extends \PHPUnit_Framework_TestCase {
    public function testExists() {
        $this->assertTrue(interface_exists(Resolvable::class));
    }
}
