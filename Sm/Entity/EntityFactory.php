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
        $result = parent::build(...func_get_args());
        return $result??new Entity;
    }
}