<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:11 AM
 */

namespace Sm\Resolvable;

use Sm\App\App;

class FunctionResolvableTest_Support {
    public function __construct(App $app) {
        
    }
    public function fn() {
        return "FN";
    }
}

/**
 * Class FunctionResolvableTest
 *
 * @package Sm\Resolvable
 */
class FunctionResolvableTest extends ResolvableTest {
    public function testCanCreate() {
        $Resolvable = new FunctionResolvable(function () { });
        $this->assertInstanceOf(FunctionResolvable::class, $Resolvable);
        return $Resolvable;
    }
    public function genericSubjectProvider() {
        return [
            [ "test" ],
            [ 1 ],
            [ null ],
            [ [] ],
        ];
    }
    /**
     * @dataProvider genericSubjectProvider
     *
     * @param $subject
     */
    public function testCanResolveCorrectly($subject) {
        $Resolvable = new FunctionResolvable(function () use ($subject) { return $subject; });
        $this->assertTrue($subject === $Resolvable->resolve());
    }
    
    public function testCanResolveClasses() {
        $Resolvable = new FunctionResolvable('\Sm\Resolvable\FunctionResolvableTest_Support::fn');
        $this->assertTrue('FN' === $Resolvable->resolve());
    }
    public function testCanSetArguments() {
        $FnResolvable = FunctionResolvable::init(PassiveResolvable::init());
        $this->assertEquals(2, $FnResolvable(2));
        $this->assertEquals(2, $FnResolvable->setArguments(2)->resolve());
    }
    public function testCanHandleArgumentsCorrectly() {
        $Resolvable = new FunctionResolvable(function ($one, $two, $three = 1) { return $one + $two + $three; });
        $this->assertEquals(6, $Resolvable->resolve(1, 2, 3));
    }
    
}