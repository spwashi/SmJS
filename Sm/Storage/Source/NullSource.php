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
    static $instance;
    public function isAuthenticated() {
        return true;
    }
    public function getName() {
        return null;
    }
    public static function init($Authentication = null) {
        # No need to keep doing this
        return isset(static::$instance) ? static::$instance : (static::$instance = parent::init($Authentication));
    }
}