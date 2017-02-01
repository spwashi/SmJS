<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:36 PM
 */

namespace Routing;


use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Request\Request;

class RoutingTest extends \PHPUnit_Framework_TestCase {
    public function testModule() {
        $App                   = App::init();
        $App->Paths->base_path = BASE_PATH;
        $app_module_path       = $App->Paths->base_path . 'Sm/App/app.sm.module.php';
        $AppModule             = Module::init(include $app_module_path ??[ ], $App);
        $App                   = $App->register('app.module', $AppModule);
    
        $App->register('request', Request::coerce('http://spwashi.com/test_test_test_test_test'));
        $routing_module = $App->Paths->base_path . 'Sm/Routing/routing.sm.module.php';
        $config         = include $routing_module;
        
        $result =
            Module::init($config, $App)
                  ->dispatch($App, 'http://spwashi.com/test_test_test_test_test');
        
        $this->assertEquals('TestFunction', $result);
    }
}
