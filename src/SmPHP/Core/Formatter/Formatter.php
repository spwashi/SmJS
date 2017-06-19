<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 4:47 PM
 */

namespace Sm\Core\Formatter;


use Sm\Core\Abstraction\Resolvable\Resolvable;

interface Formatter extends Resolvable {
    /**
     *
     *
     * @param array $_
     *
     * @return mixed
     */
    public function resolve($_ = []);
}