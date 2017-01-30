<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:56 PM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

abstract class Resolvable implements \Sm\Abstraction\Resolvable\Resolvable {
    const RESOLUTION_MODE_STD   = 1;
    const RESOLUTION_MODE_ARRAY = 2;
    
    protected $subject;
    
    public function __construct($subject) {
        $this->subject = $subject;
    }
    
    public function reset() {
        return $this;
    }
    public function resolve($arguments = null) {
        throw new UnresolvableError("No specified way to interpret this information.");
    }
}