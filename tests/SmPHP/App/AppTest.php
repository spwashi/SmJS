<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:29 AM
 */

namespace Sm\Core\Application;


use Sm\Communication\Routing\Router;
use Sm\Core\Application\Module\StandardModule;
use Sm\Core\Resolvable\FunctionResolvable;

class AppTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $App = App::init();
        $this->assertInstanceOf(App::class, $App);
        $App = new App;
        $this->assertInstanceOf(App::class, $App);
    }
    public function testCanOwnModules() {
        $App                = App::init();
        $Module             = StandardModule::init([
                                               'init'     => FunctionResolvable::coerce(function () { }),
                                               'dispatch' => FunctionResolvable::coerce(function () { }),
                                           ]);
        $App->Modules->test = $Module;
        /** @var StandardModule $Module */
        $Module = $App->Modules->test;
        $this->assertInstanceOf(StandardModule::class, $Module);
        $this->assertInstanceOf(App::class, $Module->getApp());
    }
    public function testCanBoot() {
        $App                  = App::init()->setName('Test');
        $App->Paths->app_path = TEST_PATH . 'ExampleApp';
        StandardModule::init(include APP_MODULE)->initialize($App);
    
        $this->assertInstanceOf(Router::class, $App->Router);
        $this->assertInstanceOf(StandardModule::class, $App->Modules->routing);
        $this->assertEquals('\\Test\\Controller\\', $App->controller_namespace);
    }
    
    public function testCanGetProperty() {
        $App = App::init()->register([
                                         'name'       => 'Test',
                                         'identifier' => function () { return 'Test2'; },
                                     ]);
        $this->assertEquals('Test', $App->name);
        $this->assertEquals('Test2', $App->resolve('identifier'));
        /** @var PathContainer $Paths */
        $Paths         = $App->Paths;
        $resolved_path = $Paths->resolve('base_path');
        $this->assertEquals(SRC_PATH, $resolved_path);
    }
    public function testCanRegisterDefaults() {
        $App                   = App::init();
        $App->name             = 'Test';
        $App->Paths->base_path = 'hello';
        $App->registerDefaults('test',
                               FunctionResolvable::coerce(function ($App) {
                                   return $App->name;
                               }),
                               true);
        $App->test_2 = 'hello';
        $App->registerDefaults('test_2', FunctionResolvable::coerce(function ($App) { return $App->name; }), true);
        $this->assertEquals($App->name,
                            $App->test);
        $this->assertEquals('hello', $App->test_2);
        $this->assertEquals('hello/', $App->Paths->base_path);
        
    }
}
