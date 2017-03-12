<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:14 PM
 */

namespace Sm\Entity;


class EntityTest extends \PHPUnit_Framework_TestCase {
    public function testCanCreate() {
        $Entity = new Entity;
        $this->assertInstanceOf(Entity::class, $Entity);
    }
}
