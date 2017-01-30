<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 8:27 PM
 */

namespace SmTest\Routing;


use Sm\Routing\Router;

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
     * @param \Sm\Routing\Router $Router
     */
    public function testCanRegister(Router $Router) {
        $Router->register([]);
        
    }
}
