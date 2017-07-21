<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 9:45 AM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\PlainStringFormatter;

class StdSqlFormatter extends PlainStringFormatter {
    
    /**
     * @param $columnSchema
     *
     * @return string
     */
    public function format($columnSchema) {
        if (is_numeric($columnSchema)) return $columnSchema;
        $result = parent::format($columnSchema); // TODO: Change the autogenerated stub
        return '"' . $result . '"';
    }
}