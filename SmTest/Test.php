<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:40 AM
 */

namespace SmTest;


use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Request\Request;
use Sm\Routing\Router;

class Test extends \PHPUnit_Framework_TestCase {
    public function testCanCreateApp() {
        $App          = App::init();
        $App->name    = 'Sm';
        $App->version = 1;
        $this->assertEquals('Sm', $App->name);
    }
    public function testCanRegisterApp() {
        $App                   = App::init();
        $App->Paths->base_path = BASE_PATH;
        $app_module_path       = $App->Paths->base_path . 'Sm/App/app.sm.module.php';
        $App->request          = Request::coerce('http://spwashi.com/localhost/Sm/fs/Hello');
        $AppModule             = Module::init(include $app_module_path ??[ ], $App);
        $App                   = $App->register('app.module', $AppModule);
        return $App;
    }
    /**
     * @param $App
     *
     * @depends testCanRegisterApp
     */
    public function testDefaultHasRouter(App $App) {
        $this->assertInstanceOf(Module::class, $App->resolve('routing.module'));
        $this->assertInstanceOf(Router::class, $App->resolve('router'));
        $module = $App->resolve('routing.module');
        $output = $module->dispatch($App);
        $this->assertEquals($output, 'Hey there!');
    }
}
