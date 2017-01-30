<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

class StringResolvable extends NativeResolvable {
    /** @var */
    protected $subject;
    
    public function __construct($subject = null) {
        if (!static::itemCanBeString($subject)) throw new UnresolvableError("Could not resolve subject");
        parent::__construct($subject);
    }
    
    public static function coerce($item) {
        if (static::itemCanBeString($item)) {
            return new static("{$item}");
        } else {
            throw new UnresolvableError("Cannot resolve resources that aren't stringifiable!");
        }
    }
    
    public function resolve($arguments = null) {
        return '' . $this->subject;
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