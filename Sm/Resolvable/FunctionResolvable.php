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
        if ($subject instanceof \Sm\Abstraction\Resolvable\Arguments) {
            $subject = $subject->getListedArguments();
        }
        if (is_string($subject) && strpos($subject, '::')) $subject = explode('::', $subject);
        if (!is_callable($subject)) throw  new UnresolvableError("Must be a callable function");
        parent::__construct($subject);
    }
    public function resolve($arguments = [ ]) {
        $arguments =
            $arguments instanceof \Sm\Abstraction\Resolvable\Arguments
                ? $arguments
                : new \Sm\Abstraction\Resolvable\Arguments(func_get_args());
        return call_user_func_array($this->subject, $arguments->getListedArguments());
    }
}