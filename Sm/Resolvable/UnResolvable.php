<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:31 AM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

/**
 * Class UnResolvable
 *
 * Throws an error on resolve
 *
 * @package Sm\Resolvable
 */
class UnResolvable extends Resolvable {
    
    /**
     * @param null $_
     *
     * @return mixed
     * @throws \Sm\Resolvable\Error\UnresolvableError
     * @internal param mixed|null|\Sm\Abstraction\Resolvable\Arguments $arguments ,..
     *
     */
    public function resolve($_ = null) {
        throw new UnresolvableError("Cannot resolve");
    }
}