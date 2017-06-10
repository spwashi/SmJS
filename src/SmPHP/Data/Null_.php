<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:18 PM
 */

namespace Sm\Data;


use Sm\Resolvable\NullResolvable;

class Null_ extends Type {
    public static function resolveType($subject) {
        return NullResolvable::coerce($subject);
    }
}