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
        if ($arguments instanceof Arguments && $arguments->length() === 1) {
            return $arguments->getListedArguments()[0];
        } else if (is_array($arguments) && count($arguments) === 1) {
            return $arguments[ key($arguments) ];
        }
        return $arguments;
    }
}