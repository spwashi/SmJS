<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Resolvable;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\Resolvable\Error\UnresolvableError;

class FunctionResolvable extends Resolvable {
    /**
     * FunctionResolvable constructor.
     *
     * @param Resolvable|string|array|Arguments $subject
     *
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function __construct($subject) {
        if ($subject instanceof Arguments) {
            $subject = $subject->_list();
            $subject = count($subject) === 1 ? $subject[0] : $subject;
        }
        parent::__construct($subject);
    }
    public function __toString() {
        return "[function]";
    }
    public function resolve($arguments = []) {
        $arguments = Arguments::coerce($arguments, func_get_args());
        $subject   = $this->subject;
        
        if (is_string($subject) && strpos($subject, '::'))
            $subject = explode('::', $subject);
        
        if (!is_callable($subject))
            throw  new UnresolvableError("Must be a callable function");
        
        return call_user_func_array($this->subject, $arguments->_list());
    }
}