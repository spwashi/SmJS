<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:11 PM
 */

namespace Sm\Storage\Container;


use Sm\Resolvable\NullResolvable;
use Sm\Resolvable\OnceRunResolvable;
use Sm\Resolvable\Resolvable;

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
     * @param \Sm\Storage\Container\Container $Container
     *
     * @return \Sm\Storage\Container\Container
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
     * @param \Sm\Storage\Container\Container $Container
     *
     * @return \Sm\Storage\Container\Container
     */
    public function testCanCopy(Container $Container) {
        $test_1_fn = function ($argument) {
            return $argument + 1;
        };
        $test_1    = OnceRunResolvable::init($test_1_fn);
        $Container->register('test.1', $test_1);
        $this->assertEquals(3, $Container->resolve('test.1', 2));
        $NewContainer = $Container->duplicate();
        $this->assertEquals(6, $NewContainer->resolve('test.1', 5));
        return $NewContainer;
    }
    
    /**
     * @param \Sm\Storage\Container\Container $Container
     *
     * @depends  testCanCreate
     */
    public function testCanCheckout(Container $Container) {
        $Container->register([ 'test'  => 1,
                               'hello' => 'Another',
                               'last'  => function () { return 'fifteen'; },
                             ]);
        
        $test_Resolvable = $Container->checkout('test');
        
        $this->assertInstanceOf(Resolvable::class, $test_Resolvable);
        $this->assertNotInstanceOf(NullResolvable::class, $test_Resolvable);
        $resolve = $test_Resolvable->resolve();
        $this->assertEquals(1, $resolve);
        $this->assertNull($test_Resolvable->resolve());
        
        $this->assertTrue($Container->checkBackIn($test_Resolvable));
        $this->assertNull($test_Resolvable);
        $this->assertEquals(1, $Container->checkout('test')->resolve());
        
        
        $this->assertEquals('Another', $Container->checkout('hello')
                                                 ->resolve());
        
        $this->assertEquals('fifteen', $Container->checkout('last')
                                                 ->resolve());
        
        
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
     * @param \Sm\Storage\Container\Container $Container
     *
     * @return \Sm\Storage\Container\Container
     */
    protected function _register_default(Container $Container) {
        $Container->register('test_string', 'string');
        $Container->registerDefaults('test_string', 'This is a thing');
        $Container->registerDefaults('other_test_string', 'This is a thing');
        $Container->register('test_fn', function () { return 'fn'; });
        $Container->register([
                                 'test_arr_1' => 1,
                                 'test_arr_2' => function () { return '2'; },
                             ]);
        return $Container;
    }
}
