<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:24 PM
 */

namespace Sm\Resolvable;


class NativeResolvable extends Resolvable {
    public function resolve($name = null) {
        return $this->subject;
    }
}