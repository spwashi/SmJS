<?php
/**
 * User: Sam Washington
 * Date: 2/9/17
 * Time: 1:26 AM
 */

namespace Sm\Entity;


use Sm\Entity\Error\NoMatchingEntityTypeError;

class Entity {
    /** @var  EntityType[] */
    protected $EntityTypes;
    public function setEntityType(EntityType $EntityType) {
        $class_name                       = get_class($EntityType);
        $this->EntityTypes[ $class_name ] = $EntityType;
        return $this;
    }
    /**
     * Get the EntityType that goes along with an entity name. If it doesn't exist, return null.
     *
     * @param $name
     *
     * @return null|\Sm\Entity\EntityType
     */
    public function getEntityType($name) {
        return $this->EntityTypes[ $name ] ?? null;
    }
    /**
     * Get the EntityType that goes along with a name. If it doesn't exist, throw an error.
     *
     * @param string $name
     *
     * @return null|\Sm\Entity\EntityType
     * @throws \Sm\Entity\Error\NoMatchingEntityTypeError
     */
    public function EntityType(string $name) {
        $result = $this->getEntityType($name);
        if ($result) {
            return $result;
        }
        throw new NoMatchingEntityTypeError("No Entity Type to match {$name}");
    }
}