<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:49 PM
 */

namespace Sm\Formatter;


use Sm\Abstraction\Formatting\Formatter;
use Sm\Error\WrongArgumentException;
use Sm\Resolvable\StringResolvable;

class PlainStringFormatter extends StringResolvable implements Formatter {
    public function resolve($variables = []) {
        if (!is_array($variables)) throw new WrongArgumentException("Must use arrays only.");
        return parent::resolve(...func_get_args());
    }
}