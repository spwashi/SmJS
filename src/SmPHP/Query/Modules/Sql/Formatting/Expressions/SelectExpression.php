<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 7:29 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Expressions;


use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;

class SelectExpression extends SqlQueryFormatter {
    
    /**
     * Return the item Formatted in the specific way
     *
     * @param $statement
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($statement): string {
        throw new UnimplementedError("+ Select Expressions");
    }
}