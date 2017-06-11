<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:33 PM
 */

namespace Sm\System_;


use Monolog\Logger;
use Sm\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;


class System_Test extends \PHPUnit_Framework_TestCase {
    public function testCanRegisterFactory() {
        System_::clear_defaults();
        
        System_::registerFactory(ResolvableFactory::class,
                                 new ResolvableFactory);
        
        $this->assertInstanceOf(Resolvable::class,
                                System_::Factory(ResolvableFactory::class)->build());
    }
    public function testCanUseLogger() {
        #todo this is more of an integration-testing thing
        $Logger = System_::Log('System');
        $this->assertInstanceOf(Logger::class, $Logger);
    }
}