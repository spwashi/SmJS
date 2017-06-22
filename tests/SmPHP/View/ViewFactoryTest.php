<?php
/**
 * User: Sam Washington
 * Date: 2/8/17
 * Time: 11:23 PM
 */

namespace Sm\Presentation\View;


class ViewFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $ViewFactory = new ViewFactory();
        $this->assertInstanceOf(ViewFactory::class, $ViewFactory);
        
    }
}
