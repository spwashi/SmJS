<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:31 AM
 */

namespace Sm\Resolvable;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\Resolvable\Error\UnresolvableError;

class UnResolvable extends Resolvable {
    
    /**
     * @param Arguments|null|mixed $arguments ,..
     *
     * @return mixed
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function resolve() {
        throw new UnresolvableError("Cannot resolve");
    }
}