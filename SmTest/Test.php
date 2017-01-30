<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:40 AM
 */

namespace SmTest;


use Sm\App\App;
use Sm\IoC\IoC;
use Sm\Resolvable\ResolvableFactory;

class Test extends \PHPUnit_Framework_TestCase {
    public function testCanCreateApp() {
        $Config = IoC::init(ResolvableFactory::init());
        $Config->register([
                              'name'    => 'Sm',
                              'version' => 1,
                          ]);
        
        $App = App::init($Config);
        $this->assertEquals('Sm', $App->name);
    }
}
