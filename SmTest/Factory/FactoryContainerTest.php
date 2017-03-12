<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 1:54 AM
 */

namespace Sm\Factory;


class FactoryContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveFactory() {
        $FactoryMock = $this->getMockBuilder(Factory::class)
                            ->setMethods([ 'build' ])->getMock();
        $FactoryMock->method('build')->willReturn('test');
        $FactoryContainer = new FactoryContainer;
        $FactoryContainer->register(Factory::class, $FactoryMock);
        $this->assertEquals('test', $FactoryContainer->resolve(Factory::class)->build());
        $this->assertEquals('test', $FactoryContainer->resolve('Factory')->build());
    }
}
