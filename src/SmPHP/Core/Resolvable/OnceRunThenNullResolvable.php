<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:34 AM
 */

namespace Sm\Core\Resolvable;


/**
 * Class OnceCalledResolvable
 *
 * Resolvable that runs a function, but only once
 *
 * @package Sm\Core\Resolvable
 */
class OnceRunThenNullResolvable extends OnceRunResolvable {
    public function resolve($_ = null) {
        $result = parent::resolve(...func_get_args());
        if (!isset($result)) return null;
        $this->last_value = null;
        return $result;
    }
}