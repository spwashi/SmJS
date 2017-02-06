<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:56 PM
 */

namespace Sm\Resolvable;


use Sm\Util;

/**
 * Class Resolvable
 *
 * Represents something that will eventually have an end value (vague, I know)
 * Basically, this class is meant to provide a consistent interface for interacting
 * with the various objects/types that we might come across in this framework.
 *
 * @package Sm\Resolvable
 */
abstract class Resolvable implements \Sm\Abstraction\Resolvable\Resolvable {
    const RESOLUTION_MODE_STD   = 1;
    const RESOLUTION_MODE_ARRAY = 2;
    
    protected $subject;
    
    /**
     * Resolvable constructor.
     *
     * @param mixed $subject
     */
    public function __construct($subject = null) {
        $this->setSubject($subject);
    }
    /**
     * Set the subject that the Resolvable is going to use as a reference
     *
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
    /**
     * Convert this to a string
     *
     * @return string
     */
    public function __toString() {
        if ($this->subject !== $this && Util::canBeString($this->subject)) {
            return "$this->subject";
        } else {
            $resolved = $this->resolve();
            if ($resolved !== $this && Util::canBeString($resolved)) {
                return "$resolved";
            } else {
                return '';
            }
        }
    }
    /**
     * Resolve this Resolvable with arguments
     *
     * @return mixed
     */
    public function __invoke() {
        return $this->resolve(...func_get_args());
    }
    /**
     * Revert a Resolvable back to its original state.
     * Only really meant to be used in cases where Resolvables are inherited
     *
     * @return $this
     */
    public function reset() {
        return $this;
    }
    /**
     * Static constructor for resolvables
     *
     * @param mixed $item
     *
     * @return static
     */
    public static function init($item = null) {
        return new static($item);
    }
    /**
     * Convert something to a Resolvable. Same thing as init usually (oops)
     *
     * @param mixed $item
     *
     * @return static|$this|Resolvable
     */
    public static function coerce($item = null) {
        if (is_a($item, static::class)) return $item;
        return static::init($item);
    }
}