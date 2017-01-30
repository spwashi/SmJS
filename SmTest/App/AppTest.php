<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:29 AM
 */

namespace SmTest\App;


use Sm\App\App;
use Sm\IoC\IoC;
use Sm\Resolvable\ResolvableFactory;

class AppTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $App = App::init(IoC::init(ResolvableFactory::init()));
        $this->assertInstanceOf(App::class, $App);
        
        $App = App::init(IoC::init(ResolvableFactory::init()));
        $this->assertInstanceOf(App::class, $App);
    }
    public function testCanGetProperty() {
        $IoC = IoC::init(ResolvableFactory::init());
        $IoC->register('name', 'Test');
        $IoC->register('identifier', function () { return 'Test2'; });
        $App = App::init($IoC);
        $this->assertEquals('Test', $App->name);
        $this->assertEquals('Test2', $App->resolve('identifier'));
        
    }
}
