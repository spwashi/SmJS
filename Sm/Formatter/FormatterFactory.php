<?php
/**
 * User: Sam Washington
 * Date: 3/5/17
 * Time: 3:40 PM
 */

namespace Sm\Formatter;


use Sm\Abstraction\Formatting\Formatter;
use Sm\Factory\Factory;

class FormatterFactory extends Factory {
    public function canCreateClass($object_type) {
        return is_a($object_type, Formatter::class, true);
    }
    /**
     * @param null $item
     *
     * @return null
     */
    public function build($item = null) {
        $result = parent::build(...func_get_args());
        return PlainStringFormatter::coerce($result);
    }
}