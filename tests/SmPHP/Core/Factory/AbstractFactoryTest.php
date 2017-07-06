<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 8:44 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Factory\Exception\WrongFactoryException;

class FactoryStub extends AbstractFactory {
    public function canCreateClass($classname) {
        return false;
    }
}

class AbstractFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCannotRegisterUnlessAllowedTo() {
        /** @var \Sm\Core\Factory\AbstractFactory $mockFactory */
        $mockFactory = new FactoryStub;
        $mockFactory->setCreationMode();
        $this->expectException(WrongFactoryException::class);
        $mockFactory->register(\stdClass::class, new \stdClass);
        $mockFactory->resolve(\stdClass::class);
    }
}
