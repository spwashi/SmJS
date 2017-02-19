<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 8:24 PM
 */

namespace Sm\Resolvable;


use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Factory\Factory;

class ResolvableFactory extends Factory {
    public function build($subject = null) {
        if ($subject instanceof Resolvable) return $subject;
        if (!is_callable($subject)) {
            if (is_string($subject)) return new StringResolvable($subject);
            if (!is_object($subject)) return new NativeResolvable($subject);
        } else {
            return new FunctionResolvable($subject);
        }
        return new NativeResolvable($subject);
    }
    public static function init() {
        return new static;
    }
    public static function coerce($item) {
        if ($item instanceof ResolvableFactory) return $item;
        return static::init();
    }
}