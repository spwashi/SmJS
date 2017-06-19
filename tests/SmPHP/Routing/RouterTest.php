<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:27 PM
 */

namespace Sm\Communication\Routing;


use Sm\Communication\Request\Request;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\StringResolvable;

class Example {
    public function returnEleven() {
        return 'eleven';
    }
}

class RouterTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Router = new Router();
        $this->assertInstanceOf(Router::class, $Router);
        
        $Router = Router::init();
        $this->assertInstanceOf(Router::class, $Router);
        return $Router;
    }
    /**
     * @depends testCanCreate
     *
     * @param \Sm\Communication\Routing\Router $Router
     */
    public function testCanRegister(Router $Router) {
        $Router->register(
            [
                Route::init(StringResolvable::init('hello'), 'hello/1'),
                [ 'pattern'    => 'hello/2',
                  'resolution' => StringResolvable::init('hello2'), ],
                [ 'api/(?:sections|dimensions|collections)' => StringResolvable::init('API example'), ],
                [ 'test' => 'TEST' ],
                [ '$' =>
                      function () {
                          return 'Nothing';
                      },
                ],
                [ '11' => FunctionResolvable::coerce('\\' . Example::class . '::returnEleven'), ],
            ]);
        $this->assertTrue(Route::init(null, 'hello/1')->matches('hello/1'));
    
        $this->assertEquals('hello2',
                            $Router->resolve(Request::init()->setUrl('hello/2')));
    
        $this->assertEquals('hello',
                            $Router->resolve(Request::init()->setUrl('hello/1')));
    
        $this->assertEquals('eleven',
                            $Router->resolve(Request::init()->setUrl('11')));
    
        $this->assertEquals('TEST',
                            $Router->resolve(Request::init()->setUrl('test')));
        
        
        $this->assertEquals('API example',
                            $Router->resolve(Request::init()->setUrl('api/sections')));
    }
}
