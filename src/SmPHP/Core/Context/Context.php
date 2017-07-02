<?php
/**
 * User: Sam Washington
 * Date: 6/19/17
 * Time: 8:40 PM
 */

namespace Sm\Core\Context;


use Sm\Core\Container\Mini\MiniContainer;
use Sm\Core\Internal\Identification\Identifiable;

/**
 * Interface Context
 *
 * Contexts are objects that tell us where we are and what we have to work with.
 * A huge portion of this framework deals with restricting access, and Contexts are one way we do it.
 * By forcing Contexts to match, we have a way of easily identifying scenarios.
 *
 * @package Sm\Core\Context
 */
interface Context extends Identifiable {
    /**
     * Get the attributes of a Context that are important for identification of that context
     * (readonly if that's an option).
     * This is what we use to know if a Context matches another one
     *
     * @return MiniContainer|null
     */
    public function readContextAttributes(): ?MiniContainer;
}