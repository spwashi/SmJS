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
     * @param $statement
     *
     * @return string
     */
    public function format($statement) {
        if (is_numeric($statement)) return $statement;
        $result = parent::format($statement); // TODO: Change the autogenerated stub
        return '"' . $statement . '"';
    }
}