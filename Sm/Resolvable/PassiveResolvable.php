<?php
/**
 * User: Sam Washington
 * Date: 1/30/17
 * Time: 2:31 AM
 */

namespace Sm\Resolvable;


use Sm\Abstraction\Resolvable\Arguments;

class PassiveResolvable extends Resolvable {
    
    /**
     * @param Arguments|null|mixed $arguments ,..
     *
     * @return mixed
     */
    public function resolve($arguments = null) {
        return $arguments;
    }
}