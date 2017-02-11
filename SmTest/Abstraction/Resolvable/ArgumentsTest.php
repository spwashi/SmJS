<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 9:02 PM
 */

namespace Sm\Test\Abstraction\Resolvable;


use Sm\Abstraction\Resolvable\Arguments;

class ArgumentsTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Arguments = new Arguments();
        $this->assertInstanceOf(Arguments::class, $Arguments);
        return $Arguments;
    }
    public function argumentProvider() {
        return [
            'empty'        => [ new Arguments() ],
            'array'        => [ new Arguments([ 'this is one', 'this is another' ]) ],
            'array_2'      => [ new Arguments([ 'this is one', 'this is another' ], [ 'second set' ]) ],
            'as_arguments' => [ new Arguments('this is one', 'this is another', 'second set') ],
        ];
    }
    /**
     * @dataProvider argumentProvider
     *
     * @param Arguments $Arguments
     */
    public function testCanGetArguments($Arguments) {
        $this->assertInternalType('array', $Arguments->_list());
    }
    
    public function testCanShift() {
        $Arguments = new Arguments([ 'hello', 'there' ]);
        $this->assertEquals('hello', $Arguments->shift());
        $this->assertEquals('there', $Arguments->_list()[0]);
    }
}
