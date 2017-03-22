<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:14 PM
 */

namespace Sm\Entity;


class EntityTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Entity\Entity $Entity ; */
    protected $Entity;
    public function setUp() {
        $this->Entity = new Entity;
    }
    public function testCanCreate() {
        $Entity = new Entity;
        $this->assertInstanceOf(Entity::class, $Entity);
    }
    public function testCanAppendEntityType() {
        /** @var \Sm\Entity\EntityType $Mock */
        $Mock = $this->getMockBuilder(EntityType::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $this->Entity->setEntityType($Mock);
        $this->assertEquals($Mock, $this->Entity->EntityType(get_class($Mock)));
    }
}
