<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 1:44 PM
 */

namespace Sm\Storage\Source;

/**
 * Class NullSource
 *
 * Represents nothing as a Source. Useful for things that define themselves.
 *
 * @package Sm\Storage\Source
 */
class NullSource extends Source {
    public function isAuthenticated() {
        return true;
    }
    public function getName() {
        return null;
    }
}