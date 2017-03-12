<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 4:04 PM
 */

namespace Sm\View\Template;


use Sm\App\App;

class PhpTemplateTest extends \PHPUnit_Framework_TestCase {
    public function testCanInclude() {
        $App                  = App::init();
        $App->name            = 'ExampleApp';
        $App->Paths->app_path = BASE_PATH . 'SmTest/ExampleApp/';
        
        $result = PhpTemplate::init('model/eg.php', $App)->resolve([ 'name' => 'Sam', ]);
        $this->assertEquals('Hello, Sam', $result);
    }
}
