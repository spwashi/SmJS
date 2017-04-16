<?php
/**
 * User: Sam Washington
 * Date: 3/23/17
 * Time: 2:31 AM
 */

namespace Sm\Entity;


use Sm\Entity\Property\PropertyHaver;
use Sm\Type\Variable_\Variable_;

/**
 * Class EntityVariable
 *
 * Variable that represents an Entity
 *
 * @package Sm\Entity
 */
class EntityTypeVariable extends Variable_ implements PropertyHaver {
    public function checkExistence($context = null) {
        return false;
    }
}