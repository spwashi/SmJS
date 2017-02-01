<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:40 AM
 */

namespace SmTest;


use Sm\App\App;
use Sm\IoC\IoC;

class Test extends \PHPUnit_Framework_TestCase {
    public function testCanCreateApp() {
        $Config = IoC::init();
        $Config->register([
                              'name'    => 'Sm',
                              'version' => 1,
                          ]);
    
        $App = App::coerce($Config);
        $this->assertEquals('Sm', $App->name);
    }
}
