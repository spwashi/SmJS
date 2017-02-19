<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:33 PM
 */

namespace Sm\System_;


use Sm\Entity\EntityFactory;
use Sm\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;

class System_Test extends \PHPUnit_Framework_TestCase {
    public function testCanRegisterFactory() {
        System_::clear_defaults();
        
        System_::registerFactory(ResolvableFactory::class,
                                 new ResolvableFactory);
        
        $this->assertInstanceOf(Resolvable::class,
                                System_::Factory(ResolvableFactory::class)->build());
        
        $this->assertNull(System_::Factory(EntityFactory::class)->build());
    }
}
