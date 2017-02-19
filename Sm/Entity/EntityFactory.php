<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:09 PM
 */

namespace Sm\Entity;


use Sm\Factory\Factory;

class EntityFactory extends Factory {
    
    public function build($operand = null) {
        return new Entity;
    }
}