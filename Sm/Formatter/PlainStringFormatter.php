<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:49 PM
 */

namespace Sm\Formatter;


use Sm\Resolvable\StringResolvable;

class PlainStringFormatter extends StringResolvable implements \Sm\Abstraction\Formatting\Formatter {
    public function resolve(array $variables = []) { return parent::resolve(...func_get_args()); }
}