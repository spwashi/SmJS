<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:27 PM
 */

namespace Sm\Core\Application\Module;


use Sm\Core\Resolvable\StringResolvable;

class ModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanCoerce() {
        $Module     = StandardModule::init();
        $Module_two = [];
        $this->assertEquals($Module, StandardModule::coerce($Module));
        $this->assertInstanceOf(StandardModule::class, StandardModule::coerce($Module_two));
        $number = 0;
    
        $Module = StandardModule::coerce([
                                     'init'     => function () use (&$number) { ++$number; },
                                     'dispatch' => StringResolvable::coerce('Hello') ]);
    
        $result = $Module->dispatch();
        
        $this->assertEquals('Hello', $result);
        $this->assertEquals(1, $number);
    
        $Module = StandardModule::coerce([
                                     'init' => function ($App, $self) {
                                         $this->assertInstanceOf(StandardModule::class, $self);
                                     } ]);
        $Module->dispatch();
    }
}
