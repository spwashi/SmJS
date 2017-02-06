<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:34 AM
 */

namespace Sm\Resolvable;


use Sm\Abstraction\Resolvable\Arguments;

/**
 * Class SingletonFunctionResolvable
 *
 * Resolvable that runs a function, but only once
 *
 * @package Sm\Resolvable
 */
class SingletonFunctionResolvable extends FunctionResolvable {
    /**
     * Has the Function already been run?
     *
     * @var bool
     */
    public $has_been_called = false;
    public $last_value      = null;
    
    /**
     * Method for what happens when the Singleton Function is cloned
     */
    public function __clone() {
        $this->reset();
    }
    public function reset() {
        $this->has_been_called = false;
        $this->last_value      = null;
        return $this;
    }
    public function resolve($arguments = null) {
        $arguments = Arguments::coerce($arguments, func_get_args());
        
        # If we've already called this function, we don't need to bother trying to call it again
        if ($this->has_been_called) {
            return $this->last_value;
        } else {
            $new_result            = call_user_func([
                                                        $this,
                                                        'parent::resolve',
                                                    ], $arguments);
            $this->has_been_called = true;
            return ($this->last_value = $new_result);
        }
    }
}