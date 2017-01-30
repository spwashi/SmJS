<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 11:56 AM
 */

namespace SmTest\Routing;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\Request\Request;
use Sm\Resolvable\PassiveResolvable;
use Sm\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Route = new Route('test');
        $this->assertInstanceOf(Route::class, $Route);
        return $Route;
    }
    
    public function testCanMatch() {
        $Route = new Route('test');
        $this->assertTrue($Route->matches('test'));
        $this->assertTrue($Route->matches('test/'));
        
        $Route = new Route('api/[a-zA-Z_\d]*');
        $this->assertTrue($Route->matches('api/'));
        $this->assertTrue($Route->matches('api'));
    
        $this->assertFalse($Route->matches('boonman'), 'garbag');
        $this->assertFalse($Route->matches('apis'), 'testing similar regex');
        
        $this->assertTrue($Route->matches('api/Sectf2O_is'));
        $this->assertFalse($Route->matches('api/s*ections'), 'garbage');
    
        $Route = new Route('api/{test}');
        $this->assertTrue($Route->matches('api/sections'), 'named parameter w no regex');
    
        $Route = new Route('api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $this->assertTrue($Route->matches('api/sections/test/10/'), 'multiple named parameters');
    
        $Route   = new Route('api/{test}:[a-zA-Z_\d]*');
        $Request = Request::init()->setUrl('http://spwashi.com/api/sections');
        $this->assertTrue($Route->matches($Request), 'matching a simple request');
    }
    
    public function testCanResolve() {
        $Route = new Route('api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $Route->setSubject(PassiveResolvable::init());
        $Request = Request::init()->setUrl('http://spwashi.com/api/pages/test/10');
        
        
        /** @var Arguments $result */
        $result = $Route->resolve($Request);
        
        $this->assertInstanceOf(Arguments::class, $result);
        $this->assertEquals('pages', $result->getParameter('test'));
        $this->assertEquals('10', $result->getParameter('id'));
    }
}
