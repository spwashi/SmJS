<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 1:54 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Resolvable\Error\UnresolvableException;

class FactoryContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolveFactory() {
        $FactoryMock = $this->getMockBuilder(AbstractFactory::class)
                            ->setMethods([ 'build' ])->getMock();
        $FactoryMock->method('build')->willReturn('test');
        $FactoryContainer = new FactoryContainer;
        $FactoryContainer->register(AbstractFactory::class, $FactoryMock);
        $this->assertEquals('test', $FactoryContainer->resolve(AbstractFactory::class)->build());
        $this->assertEquals('test', $FactoryContainer->resolve('AbstractFactory')->build());
    
        $this->expectException(UnresolvableException::class);
        $FactoryContainer->resolve('DoesnotExistFactory')->build();
    }
}
