<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:27 PM
 */

namespace SmTest\App\Module;


use Sm\App\Module\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanCoerce() {
        $ResFact   = Module::init();
        $ResFact_3 = [ ];
        $this->assertEquals($ResFact, Module::coerce($ResFact));
        $this->assertInstanceOf(Module::class, Module::coerce($ResFact_3));
    }
}
