<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:52 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Error\WrongArgumentException;

class PropertyContainerTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Property\PropertyContainer */
    protected $PropertyContainer;
    public function setUp() {
        $this->PropertyContainer = new PropertyContainer;
    }
    public function testCanRegisterProperty() {
        $Property = new Property;
        $this->PropertyContainer->register('title', $Property);
        
        $this->expectException(WrongArgumentException::class);
        $this->PropertyContainer->register('first_name', new \stdClass);
    }
    public function testCanRemoveProperty() {
        $this->testCanRegisterProperty();
        $Property = $this->PropertyContainer->title;
        $this->assertInstanceOf(Property::class, $Property);
    }
    public function testCanSetPropertyHaver() {
        /** @var \Sm\Data\Property\PropertyHaver $PropertyHaver */
        $PropertyHaver = $this->getMockBuilder(PropertyHaver::class)->getMock();
        $this->PropertyContainer->setPropertyHaver($PropertyHaver);
        $this->assertEquals($PropertyHaver, $this->PropertyContainer->getPropertyHaver());
        
        $PropertyContainer             = $this->PropertyContainer;
        $PropertyContainer->first_name = new Property;
        foreach ($PropertyContainer as $Property) {
            $this->assertContains($PropertyHaver, $Property->getPropertyHavers());
        }
    }
    public function testCanMarkReadonlyAndNotRegister() {
        $this->PropertyContainer->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->testCanRegisterProperty();
    }
    public function testCanMarkReadonlyAndNotRemove() {
        $this->PropertyContainer->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->testCanRemoveProperty();
    }
}
