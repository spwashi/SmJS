<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 9:19 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Internal\Identification\Identifiable;

/**
 * Interface Resolvable
 *
 * Something that resolves to a value
 *
 * @package Sm\Core\Resolvable
 */
interface Resolvable extends Identifiable {
    /**
     * Get the end value of a Resolvable
     *
     * @param null $_
     *
     * @return mixed
     */
    public function resolve($_ = null);
    public function reset();
    public function getSubject();
    public function setSubject($subject);
}