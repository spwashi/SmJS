<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:49 PM
 */

namespace Sm\Core\Formatter;


use Sm\Core\Error\WrongArgumentException;
use Sm\Core\Resolvable\StringResolvable;

class PlainStringFormatter extends StringResolvable implements Formatter {
    public function resolve($_ = []) {
        if (!is_array($_)) throw new WrongArgumentException("Must use arrays only.");
        return parent::resolve(...func_get_args());
    }
}