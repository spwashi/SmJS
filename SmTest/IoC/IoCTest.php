<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:11 PM
 */

namespace SmTest\IoC;


use Sm\IoC\IoC;
use Sm\Resolvable\ResolvableFactory;

class IoCTest extends \PHPUnit_Framework_TestCase {
    public function setUp() { ; }
    
    public function testCanCreate() {
        $IoC = new IoC;
        $this->assertInstanceOf(IoC::class, $IoC);
        $IoC = IoC::init(new ResolvableFactory());
        $this->assertInstanceOf(IoC::class, $IoC);
        return $IoC;
    }
    
    /**
     * @depends      testCanCreate
     * @dataProvider IoC_Provider
     *
     * @param \Sm\IoC\IoC $IoC
     *
     * @return \Sm\IoC\IoC
     */
    public function testCanRegister(IoC $IoC) {
        return $this->_register_default($IoC);
    }
    /**
     *
     * @return array
     */
    public function IoC_Provider() {
        $IoC = IoC::init(new ResolvableFactory());
        $this->_register_default($IoC);
        return [
            'original'  => [ $IoC ],
            'duplicate' => [ $IoC->duplicate() ],
        ];
    }
    
    /**
     * @dataProvider IoC_Provider
     *
     * @param IoC $IoC
     */
    public function testCanResolve(IoC $IoC) {
        $string_result = $IoC->resolve('test_string');
        $this->assertEquals("string", $string_result);
        
        $fn_result = $IoC->resolve('test_fn');
        $this->assertEquals("fn", $fn_result);
        
        $test_arr_1_result = $IoC->resolve('test_arr_1');
        $this->assertEquals(1, $test_arr_1_result);
        
        $test_arr_2_result = $IoC->resolve('test_arr_2');
        $this->assertEquals("2", $test_arr_2_result);
        
    }
    
    /**
     * @depends testCanCreate
     *
     * @param \Sm\IoC\IoC $IoC
     *
     * @return \Sm\IoC\IoC
     */
    public function testCanCopy(IoC $IoC) {
        return $IoC->duplicate();
    }
    
    public function tearDown() { }
    /**
     * @param \Sm\IoC\IoC $IoC
     *
     * @return \Sm\IoC\IoC
     */
    protected function _register_default(IoC $IoC) {
        $IoC->register('test_string', 'string');
        $IoC->register('test_fn', function () { return 'fn'; });
        $IoC->register([
                           'test_arr_1' => 1,
                           'test_arr_2' => function () { return '2'; },
                       ]);
        return $IoC;
    }
}
