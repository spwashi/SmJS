<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:00 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Abstraction\Formatting\Formattable;

abstract class SqlFragment implements Formattable {
    public $is_final = false;
    public function setVariables(array $variables) {
        foreach ($variables as $index => $variable) {
            $this->$index = $variable;
        }
        return $this;
    }
    public static function init() {
        return new static;
    }
    /**
     * Given one SqlFragment, create another of this class type using its variables.
     *
     * @param \Sm\Storage\Modules\Sql\Formatter\SqlFragment $sql_fragment
     *
     * @return $this
     */
    public static function inherit(SqlFragment $sql_fragment) {
        return static::init()->setVariables($sql_fragment->getVariables());
    }
}