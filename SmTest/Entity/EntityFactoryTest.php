<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:13 PM
 */

namespace Sm\Test\Entity;


use Sm\Entity\Entity;
use Sm\Entity\EntityFactory;

class EntityFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCanBuild() {
        $EntityFactory = new EntityFactory();
        $this->assertInstanceOf(Entity::class, $EntityFactory->build());
    }
}
