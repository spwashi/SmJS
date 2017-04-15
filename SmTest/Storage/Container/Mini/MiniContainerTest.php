<?php
/**
 * User: Sam Washington
 * Date: 4/14/17
 * Time: 9:30 PM
 */

namespace Sm\Storage\Container\Mini;


class MiniContainerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Storage\Container\Mini\MiniContainer $MiniContainer */
    protected $MiniContainer;
    public function setUp() {
        $this->MiniContainer = new MiniContainer;
    }
    public function testCanRegister() {
        $this->MiniContainer->register('sam', 'test');
        $this->MiniContainer->register('hello', [ 'world' ]);
        $this->MiniContainer->set_test = 'This should be registered';
        return $this->MiniContainer;
    }
    /**
     * @param \Sm\Storage\Container\Mini\MiniContainer $results
     *
     * @depends  testCanRegister
     */
    public function testCanResolve($results) {
        $this->assertTrue($results->canResolve('sam'));
        $this->assertFalse($results->canResolve('fake'));
        $this->assertEquals('test', $results->resolve('sam'));
        $this->assertEquals('test', $results->sam);
        $this->assertEquals('This should be registered', $results->set_test);
        $this->assertEquals([ 'world' ], $results->hello);
    }
    /**
     * @depends testCanRegister
     */
    public function testCanGetAll() {
        $this->assertInternalType('array', $this->MiniContainer->getAll());
    }
}
