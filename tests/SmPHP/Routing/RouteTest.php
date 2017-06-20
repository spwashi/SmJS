<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 11:56 AM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Network\Http\HttpRequest;
use Sm\Core\Resolvable\PassiveResolvable;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\Resolvable\UnResolvable;

class RouteTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Route = new Route(null, 'test');
        $this->assertInstanceOf(Route::class, $Route);
        return $Route;
    }
    
    public function testCanMatch() {
        $Route = new Route(null, 'test');
        $this->assertTrue($Route->matches('test'));
        $this->assertTrue($Route->matches('test/'));
    
        $Route = new Route(null, 'api/[a-zA-Z_\d]*');
        $this->assertTrue($Route->matches('api/'));
        $this->assertTrue($Route->matches('api'));
    
        $this->assertFalse($Route->matches('boonman'), 'garbag');
        $this->assertFalse($Route->matches('apis'), 'testing similar regex');
        
        $this->assertTrue($Route->matches('api/Sectf2O_is'));
        $this->assertFalse($Route->matches('api/s*ections'), 'garbage');
    
        $Route = new Route(null, 'api/{test}');
        $this->assertTrue($Route->matches('api/sections'), 'named parameter w no regex');
    
        $Route = new Route(null, '11');
        $this->assertTrue($Route->matches('11'), 'number');
    
    
        $Route = new Route(null, 'api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $this->assertTrue($Route->matches('api/sections/test/10/'), 'multiple named parameters');
    
        $Route   = new Route(null, 'api/{test}:[a-zA-Z_\d]*');
        $Request = HttpRequest::init()->setUrl('http://spwashi.com/api/sections');
        $this->assertTrue($Route->matches($Request), 'matching a simple request');
    }
    public function testCanCoerce() {
        $Route = Route::init([ 'resolution' => StringResolvable::init('item'), 'pattern' => 'hello' ]);
        $this->assertEquals('item', $Route->resolve(HttpRequest::init('hello')));
        $this->assertNotEquals('it@em', $Route->resolve(HttpRequest::init('hello')));
    
        $Route = Route::init([
                                 'resolution' => UnResolvable::init(),
                                 'pattern'    => 'hello',
                                 'default'    => StringResolvable::init('default'),
                             ]);
        $this->assertEquals('default', $Route->resolve(HttpRequest::init('hello')));
        $this->assertNotEquals('defaul2t', $Route->resolve(HttpRequest::init('hello')));
    }
    public function testCanResolve() {
        $Route = new Route(null, 'api/{test}:[a-zA-Z_\d]*/test/{id}:[\d]*');
        $Route->setResolution(PassiveResolvable::init());
        $Request = HttpRequest::init('http://spwashi.com/api/pages/test/10');
    
        $Route = Route::init(StringResolvable::init('hello'), '11');
        $Route->resolve(HttpRequest::init('11'));
        
    }
}
