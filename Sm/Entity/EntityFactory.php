<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:09 PM
 */

namespace Sm\Entity;


use Sm\Factory\Factory;

class EntityFactory extends Factory {
    
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return mixed
     */
    public function build($operand = null) {
        return new Entity;
    }
}