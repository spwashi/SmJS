<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:11 PM
 */

namespace Sm\Container;


use Sm\Resolvable\SingletonFunctionResolvable;

class ContainerTest extends \PHPUnit_Framework_TestCase {
    public function setUp() { ; }
    public function tearDown() { }
    public function testCanCreate() {
        $Container = new Container;
        $this->assertInstanceOf(Container::class, $Container);
        $Container = Container::init();
        $this->assertInstanceOf(Container::class, $Container);
        return $Container;
    }
    /**
     * @depends      testCanCreate
     * @dataProvider Container_Provider
     *
     * @param \Sm\Container\Container $Container
     *
     * @return \Sm\Container\Container
     */
    public function testCanRegister(Container $Container) {
        return $this->_register_default($Container);
    }
    /**
     *
     * @return array
     */
    public function Container_Provider() {
        $Container = Container::init();
        $this->_register_default($Container);
        return [
            'original'  => [ $Container ],
            'duplicate' => [ $Container->duplicate() ],
        ];
    }
    /**
     * @dataProvider Container_Provider
     *
     * @param Container $Container
     */
    public function testCanResolve(Container $Container) {
        $string_result = $Container->resolve('test_string');
        $this->assertEquals("string", $string_result);
        
        $string_result = $Container->resolve('other_test_string');
        $this->assertEquals('This is a thing', $string_result);
        
        $fn_result = $Container->resolve('test_fn');
        $this->assertEquals("fn", $fn_result);
        
        $test_arr_1_result = $Container->resolve('test_arr_1');
        $this->assertEquals(1, $test_arr_1_result);
        
        $test_arr_2_result = $Container->resolve('test_arr_2');
        $this->assertEquals("2", $test_arr_2_result);
        
    }
    /**
     * @depends testCanCreate
     *
     * @param \Sm\Container\Container $Container
     *
     * @return \Sm\Container\Container
     */
    public function testCanCopy(Container $Container) {
        $Container->register('test.1', SingletonFunctionResolvable::init(function ($argument) {
            return $argument + 1;
        }));
        $this->assertEquals(3, $Container->resolve('test.1', 2));
        $NewContainer = $Container->duplicate();
        $this->assertEquals(6, $NewContainer->resolve('test.1', 5));
        return $NewContainer;
    }
    public function testCanIterate() {
        $end       = [];
        $array     = [ 'sam', 'bob', 'jan' ];
        $Container = Container::init();
        $Container->register($array);
        
        foreach ($Container as $index => $item) $end[] = $item->resolve();
        
        $this->assertEquals($array, $end);
    }
    /**
     * @param \Sm\Container\Container $Container
     *
     * @return \Sm\Container\Container
     */
    protected function _register_default(Container $Container) {
        $Container->register('test_string', 'string');
        $Container->register_defaults('test_string', 'This is a thing');
        $Container->register_defaults('other_test_string', 'This is a thing');
        $Container->register('test_fn', function () { return 'fn'; });
        $Container->register([
                                 'test_arr_1' => 1,
                                 'test_arr_2' => function () { return '2'; },
                             ]);
        return $Container;
    }
}
