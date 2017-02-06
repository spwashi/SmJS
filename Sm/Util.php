<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:38 PM
 */

namespace Sm;


class Util {
    /**
     * Is there a good way for us to convert this into a string?
     *
     * @param $var
     *
     * @return bool
     */
    public static function canBeString($var) {
        return $var === null || is_scalar($var) || is_callable([ $var, '__toString' ]);
    }
}