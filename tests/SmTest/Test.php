<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:40 AM
 */

namespace Sm;


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
        /** @var App $App */
        $App                  = App::init();
        $App->name            = 'ExampleApp';
        $App->Paths->app_path = EXAMPLE_APP_PATH;
        $app_module_path      = $App->Paths->base_path . 'Sm/App/app.sm.module.php';
        $App->Request         = Request::coerce('http://spwashi.com/Sm/ea/Hello');
        $App->Modules->_app   = include $app_module_path ??[];
        return $App;
    }
    /**
     * @param $App
     *
     * @depends testCanRegisterApp
     */
    public function testDefaultHasRouter(App $App) {
        $this->assertInstanceOf(Module::class, $App->Modules->routing);
        $this->assertInstanceOf(Router::class, $App->Router);
    
        $this->assertEquals('Hey there!',
                            $App->Modules->routing($App->Request));
    }
}
