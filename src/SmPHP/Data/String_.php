<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:17 PM
 */

namespace Sm\Data;


use Sm\Resolvable\StringResolvable;

class String_ extends Type {
    public static function resolveType($subject) {
        return StringResolvable::coerce($subject);
    }
}