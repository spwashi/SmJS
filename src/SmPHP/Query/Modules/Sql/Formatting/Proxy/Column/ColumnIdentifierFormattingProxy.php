<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Column;


use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableIdentifierFormattingProxy;

/**
 * Class ColumnFormattingProxy
 *
 * Class that is going to help tell us stuff about an item in the context of being a column
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
abstract class ColumnIdentifierFormattingProxy extends SqlFormattingProxy {
    abstract public function getTable(): ?TableIdentifierFormattingProxy;
    abstract public function getColumnName(): ?string;
}