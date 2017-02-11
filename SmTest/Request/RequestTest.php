<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:56 PM
 */

namespace Sm\Test\Request;


use Sm\Request\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Request = Request::init();
        $this->assertInstanceOf(Request::class, $Request);
        return $Request;
    }
    
    /**
     * @param Request $Request
     *
     * @depends testCanCreate
     */
    public function testCanSetUrl($Request) {
        $Request->setUrl('http://spwashi.com');
        $this->assertEquals('http://spwashi.com', $Request->getUrl());
    }
    
    public function testCanGetPathCorrectly() {
        $Request = Request::init();
        
        $Request->setUrl('http://spwashi.com/this/is/a/thing');
        $this->assertEquals('this/is/a/thing', $Request->getUrlPath());
        
        $Request->setUrl('//spwashi.com/this/is/a/thing');
        $this->assertEquals('this/is/a/thing', $Request->getUrlPath());
        $Request->setChangePath('this/is');
        $this->assertEquals('a/thing', $Request->getUrlPath());
    
        $Request->setUrl('one/two/three/four');
        $this->assertEquals('one/two/three/four', $Request->getUrlPath());
        
    }
}
