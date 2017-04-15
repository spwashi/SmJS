<?php
/**
 * User: Sam Washington
 * Date: 3/16/17
 * Time: 12:32 AM
 */

namespace Sm\Entity\Property;

use Sm\Abstraction\Identifier\Identifiable;


/**
 * Interface PropertyHaver
 *
 * Represents something that holds a specific set of properties
 *
 * @package Sm\Entity\Property
 */
interface PropertyHaver extends Identifiable {
    /**
     * Based on some sort of context (e.g. A source or a Source name),
     * check whether or not this PropertyHaver exists.
     *
     * @param string|object|null $context
     *
     * @return mixed
     */
    public function checkExistence($context = null);
}