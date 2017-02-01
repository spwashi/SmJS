<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

class StringResolvable extends NativeResolvable implements \JsonSerializable {
    /** @var */
    protected $value;
    public function __construct($subject = null) {
        if (!static::itemCanBeString($subject)) throw new UnresolvableError("Could not resolve subject");
        parent::__construct($subject);
    }
    public function __debugInfo() {
        return [ 'value' => $this->value ?? null ];
    }
    public function __toString() {
        return $this->value;
    }
    public static function coerce($item = null) {
        return static::itemCanBeString($item) ? new static("{$item}") : new static;
    }
    public function resolve($arguments = null) {
        return '' . $this->value;
    }
    public function jsonSerialize() {
        return 'Hello';
    }
    /**
     * Function to determine whether something can be a string
     * ::UTIL::
     *
     * @param $var
     *
     * @return bool
     */
    protected static function itemCanBeString($var) {
        return $var === null || is_scalar($var) || is_callable([ $var, '__toString' ]);
    }
}