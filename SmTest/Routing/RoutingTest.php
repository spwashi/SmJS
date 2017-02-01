<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:36 PM
 */

namespace Routing;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\App\App;
use Sm\App\Module\Module;
use Sm\Request\Request;

class RoutingTest extends \PHPUnit_Framework_TestCase {
    public function testModule() {
        $App = App::init();
        $App->Paths;
        $App->Paths->register(
            [
                'base_path'   => BASE_PATH,
                'config_path' => BASE_PATH . 'Sm/config/default/',
            ]);
        $routing_module = $App->Paths->base_path . 'Sm/Routing/routing.sm.module.php';
        $config         = include $routing_module;
        
        $Request   = Request::coerce('http://spwashi.com/test_test_test_test_test');
        $Arguments =
            Arguments::coerce()
                     ->setParameter('Request', $Request);
        
        $result =
            Module::init($config, $App)
                  ->dispatch($App, $Arguments);
        
        $this->assertEquals('TestFunction', $result);
    }
}
