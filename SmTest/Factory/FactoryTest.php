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
        $Factory->register($return_eleven);
        $response = $Factory->build();
        $this->assertEquals(11, $response);
        
        $add_1   = function (int $int) { return $int + 1; };
        $Factory = new Factory();
        $Factory->register($add_1);
        $response = $Factory->build(2);
        $this->assertEquals(3, $response);
        $response = $Factory->build(1);
        $this->assertNotEquals(3, $response);
        
        $Mock = $this->getMockBuilder(\stdClass::class)
                     ->setMethods([ 'test' ])->getMock();
        $Mock->method('test')->willReturn('test_works');
        
        $Factory = new Factory();
        $Factory->register($Mock, \stdClass::class);
        $MockFromFactory = $Factory->build(\stdClass::class);
        $this->assertEquals('test_works', $MockFromFactory->test());
    }
}
