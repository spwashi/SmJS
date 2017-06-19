<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 11:46 PM
 */

namespace Sm\Communication\Response\View;


use Sm\Communication\Response\View\Template\PhpTemplate;
use Sm\Core\Application\App;

class ViewTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $App                  = App::init();
        $App->name            = 'ExampleApp';
        $App->Paths->app_path = EXAMPLE_APP_PATH;
        $Template             = PhpTemplate::init('model/eg.php', $App);
        
        $View = View::init([ 'name' => 'Eric', ])->setTemplate($Template);
        $this->assertEquals('Hello, Eric', $View->resolve());
    }
}
