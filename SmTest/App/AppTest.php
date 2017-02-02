<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:29 AM
 */

namespace SmTest\App;


use Sm\App\App;
use Sm\App\Module\Module;
use Sm\IoC\IoC;
use Sm\Resolvable\FunctionResolvable;
use Sm\Routing\Router;

class AppTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $App = App::init();
        $this->assertInstanceOf(App::class, $App);
    
        $App = App::init();
        $this->assertInstanceOf(App::class, $App);
    }
    public function testCanOwnModules() {
        $App    = App::init();
        $Module = Module::init([
                                   'init'     => FunctionResolvable::coerce(function () { }),
                                   'dispatch' => FunctionResolvable::coerce(function () { }),
                               ]);
        $App->register('test.module', $Module);
        /** @var Module $Module */
        $Module = $App->resolve('test.module');
        $this->assertInstanceOf(Module::class, $Module);
        $this->assertInstanceOf(App::class, $Module->getApp());
    }
    public function testCanBoot() {
        $App                   = App::init();
        $App->Paths->base_path = BASE_PATH;
        
        $app_module = $App->Paths->base_path . 'Sm/App/app.sm.module.php';
        $config     = include $app_module;
        $Module     = Module::init($config)->setApp($App);
        $this->assertNull($App->resolve('router'));
        $Module->initialize();
        $this->assertInstanceOf(Router::class, $App->resolve('router'));
        $this->assertInstanceOf(Module::class, $App->resolve('routing.module'));
    }
    
    public function testCanGetProperty() {
        $IoC = IoC::init();
        $IoC->register('name', 'Test');
        $IoC->register('identifier', function () { return 'Test2'; });
        $App = App::init()->register([
                                         'name'       => 'Test',
                                         'identifier' => function () { return 'Test2'; },
                                     ]);
        $this->assertEquals('Test', $App->name);
        $this->assertEquals('Test2', $App->resolve('identifier'));
        $resolved_path = $App->Paths->resolve('base_path');
        $this->assertEquals(null, $resolved_path);
    
    
        $IoC   = IoC::init();
        $Paths = IoC::init()->register([ 'base_path' => __DIR__ ]);
        $IoC->register('Paths', $Paths);
        $App           = App::coerce($IoC);
        $resolved_path = $App->Paths->resolve('base_path');
        $this->assertEquals(__DIR__, $resolved_path);
    }
    public function testCanRegisterDefalts() {
        $App                   = App::init();
        $App->Paths->base_path = 'hello';
        $this->assertEquals('hello/', $App->Paths->base_path);
    }
}
