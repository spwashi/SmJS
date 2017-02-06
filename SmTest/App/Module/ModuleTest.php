<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:27 PM
 */

namespace SmTest\App\Module;


use Sm\App\Module\Module;
use Sm\Resolvable\StringResolvable;

class ModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanCoerce() {
        $Module     = Module::init();
        $Module_two = [ ];
        $this->assertEquals($Module, Module::coerce($Module));
        $this->assertInstanceOf(Module::class, Module::coerce($Module_two));
        $number = 0;
    
        $Module = Module::coerce([
                                     'init'     => function () use (&$number) { ++$number; },
                                     'dispatch' => StringResolvable::coerce('Hello') ]);
    
        $result = $Module->dispatch();
        
        $this->assertEquals('Hello', $result);
        $this->assertEquals(1, $number);
    }
}
