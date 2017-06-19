<?php
/**
 * User: Sam Washington
 * Date: 2/8/17
 * Time: 10:36 PM
 */

namespace Sm\Communication\Response\Output;

ob_start();

class OutputTest extends \PHPUnit_Framework_TestCase {
    public function testCanOutput() {
        $this->expectOutputString('hello');
        Output::out('hello');
    }
}
