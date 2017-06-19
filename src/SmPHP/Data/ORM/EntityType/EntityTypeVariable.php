<?php
/**
 * User: Sam Washington
 * Date: 3/23/17
 * Time: 2:31 AM
 */

namespace Sm\Data\ORM\EntityType;


use Sm\Data\Datatype\Variable_\Variable_;
use Sm\Data\Property\PropertyHaver;

/**
 * Class EntityVariable
 *
 * Variable that represents an Entity
 *
 * @package Sm\Data\ORM\EntityType
 */
class EntityTypeVariable extends Variable_ implements PropertyHaver {
    public function checkExistence($context = null) {
        return false;
    }
}