<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:05 PM
 */

namespace Sm\Response;


use Sm\Data\DateTime_;

class ResponseTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Response = Response::coerce();
        $this->assertInstanceOf(Response::class, $Response);
    }
    public function testCanGetDate() {
        $Response = Response::coerce();
        $Date     = $Response->getCreationDt();
        $this->assertInstanceOf(DateTime_::class, $Date);
    }
    public function testCanResolve() {
        $Response = Response::coerce('string');
        $this->assertEquals('string', $Response->resolve());
        
        $Response = Response::coerce([ 'string' ]);
        $Response->setContentType(Response::TYPE_JSON);
        $this->assertJson($Response->resolve());
        $Response->setContentType(Response::TYPE_TEXT_HTML);
        $this->assertInternalType('string', $Response->resolve());
    }
}
