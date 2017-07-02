<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 1:53 PM
 */

namespace Sm\Core\Formatting\Formatter;


class FormatterFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanBuild() {
        $formatterFactory = new FormatterFactory();
        $result           = $formatterFactory->build('This is a test');
        
        # By default returns PlainStringFormatter
        # todo why??
        $this->assertInstanceOf(PlainStringFormatter::class, $result);
    }
}
