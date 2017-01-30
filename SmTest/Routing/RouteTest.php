<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 11:56 AM
 */

namespace SmTest\Routing;


use Sm\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Route = new Route('test');
        $this->assertInstanceOf(Route::class, $Route);
        return $Route;
    }
    
    /**
     * @depends  testCanCreate
     *
     */
    public function testCanMatch() {
        $Route = new Route('test');
        $this->assertTrue($Route->matches('test'));
        $this->assertTrue($Route->matches('test/'));
        
        $Route = new Route('api/[a-zA-Z_\d]*');
        $this->assertTrue($Route->matches('api/'));
        $this->assertTrue($Route->matches('api'));
        
        $this->assertFalse($Route->matches('boonman'));
        $this->assertFalse($Route->matches('apis'));
        
        $this->assertTrue($Route->matches('api/Sectf2O_is'));
        $this->assertFalse($Route->matches('api/s*ections'));
        
        $Route = new Route('api/{test}:[a-zA-Z_\d]*');
        $this->assertTrue($Route->matches('api/sections'));
    }
}
