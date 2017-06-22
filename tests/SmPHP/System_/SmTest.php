<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:33 PM
 */

namespace Sm\Core\System_;


use Monolog\Logger;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;


class SmTest extends \PHPUnit_Framework_TestCase {
    public function testCanRegisterFactory() {
        Sm::registerFactory(ResolvableFactory::class,
                            new ResolvableFactory);
        
        $this->assertInstanceOf(Resolvable::class,
                                Sm::resolveFactory(ResolvableFactory::class)->build());
    }
    public function testCanUseLogger() {
        #todo this is more of an integration-testing thing
        $Logger = Sm::Log('System');
        $this->assertInstanceOf(Logger::class, $Logger);
    }
}