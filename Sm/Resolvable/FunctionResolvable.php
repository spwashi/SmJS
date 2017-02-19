<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

class FunctionResolvable extends Resolvable {
    public function __toString() {
        return "[function]";
    }
    public function resolve() {
        $arguments = func_get_args();
        $subject   = $this->subject;
        
        if (is_string($subject) && strpos($subject, '::'))
            $subject = explode('::', $subject);
        
        if (!is_callable($subject))
            throw  new UnresolvableError("Must be a callable function");
        
        return call_user_func_array($this->subject, $arguments);
    }
}