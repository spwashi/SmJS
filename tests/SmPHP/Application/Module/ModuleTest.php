<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:27 PM
 */

namespace Sm\Application\Module;


use Sm\Core\Resolvable\StringResolvable;

class ModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanCoerce() {
        $Module     = StandardModule::init();
        $Module_two = [];
        $this->assertEquals($Module, StandardModule::init($Module));
        $this->assertInstanceOf(StandardModule::class, StandardModule::init($Module_two));
        $number = 0;
    
        $Module = StandardModule::init([
                                           'init'     => function () use (&$number) { ++$number; },
                                           'dispatch' => StringResolvable::init('Hello') ]);
    
        $result = $Module->dispatch();
        
        $this->assertEquals('Hello', $result);
        $this->assertEquals(1, $number);
    
        $Module = StandardModule::init([
                                     'init' => function ($App, $self) {
                                         $this->assertInstanceOf(StandardModule::class, $self);
                                     } ]);
        $Module->dispatch();
    }
}
