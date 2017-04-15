<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:26 PM
 */

namespace Sm\Factory;


class FactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Factory = new Factory;
        $this->assertInstanceOf(Factory::class, $Factory);
    }
    public function testCanCreateClasses() {
        $Factory = new Factory;
        $this->assertInstanceOf(Factory::class, $Factory->build(Factory::class));
    }
    public function testCanRegister() {
        $return_eleven = function () { return 11; };
        $Factory       = new Factory();
        $Factory->register(null, $return_eleven);
        $response = $Factory->build();
        $this->assertEquals(11, $response);
    
    
        $Factory = new Factory();
    
        $add_1 = function (int $int) { return $int + 1; };
        $Factory->register(null, $add_1);
        $response_1 = $Factory->build(2);
        $response_2 = $Factory->build(1);
        $this->assertEquals(3, $response_1);
        $this->assertNotEquals(3, $response_2);
        
        $Mock = $this->getMockBuilder(\stdClass::class)
                     ->setMethods([ 'test' ])->getMock();
        $Mock->method('test')->willReturn('test_works');
    
        ###
        $Factory = new Factory();
        $Factory->register(\stdClass::class, $Mock);
        $MockFromFactory = $Factory->build(\stdClass::class);
        $this->assertEquals('test_works', $MockFromFactory->test());
        ###
        $Factory = new Factory();
        $Factory->register(\DOMAttr::class, function () { return 'hello'; });
        $this->assertEquals('hello', $Factory->build(new \DOMAttr('name')));
        
    }
}
