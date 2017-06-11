<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 5:34 PM
 */

namespace Sm\Entity;


use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;

class EntityTypeMetaTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Entity\EntityTypeMeta $EntityTypeMeta */
    public $EntityTypeMeta;
    public function setUp() {
        $this->EntityTypeMeta = EntityTypeMeta::init();
        $this->EntityTypeMeta->setProperties(new PropertyContainer);
    }
    public function testDoesHavePropertyContainer() {
        $this->assertInstanceOf(PropertyContainer::class, $this->EntityTypeMeta->getProperties());
    }
    public function testCanRegisterProperties() {
        $this->EntityTypeMeta->Properties->first_name = new Property;
        
        $this->assertEquals('first_name',
                            $this->EntityTypeMeta->Properties->first_name->name);
        
        # Also  test to see if cloning works properly
        $clone = clone $this->EntityTypeMeta;
        $this->assertEquals('first_name',
                            $clone->Properties->first_name->name);
        
        $self_fn  = $this->EntityTypeMeta->Properties->first_name;
        $clone_fn = $clone->Properties->first_name;
        
        # The clone should not have the same properties
        $this->assertFalse($self_fn === $clone_fn);
    }
}
