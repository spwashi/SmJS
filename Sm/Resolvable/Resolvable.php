<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:56 PM
 */

namespace Sm\Resolvable;


abstract class Resolvable implements \Sm\Abstraction\Resolvable\Resolvable {
    const RESOLUTION_MODE_STD   = 1;
    const RESOLUTION_MODE_ARRAY = 2;
    
    protected $value;
    
    public function __construct($subject = null) {
        $this->value = $subject;
    }
    public function __toString() {
        return json_encode($this);
    }
    public function __invoke($_ = null) {
        return $this->resolve(func_get_args());
    }
    public function reset() {
        return $this;
    }
    public static function init($item = null) {
        return new static($item);
    }
    public static function coerce($item = null) {
        if (is_a($item, static::class)) return $item;
        return static::init($item);
    }
}