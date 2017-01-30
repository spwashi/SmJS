<?php
/**
 * User: spwashi2
 * Date: 1/26/2017
 * Time: 3:11 PM
 */

namespace Sm\Resolvable;


class NullResolvable extends NativeResolvable {
    public function resolve($arguments = null) {
        return null;
    }
}