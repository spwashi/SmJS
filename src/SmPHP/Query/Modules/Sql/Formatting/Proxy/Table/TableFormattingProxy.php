<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 8:48 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Table;

use Sm\Core\Formatting\FormattingProxy;


/**
 * Class TableFormattingProxy
 *
 * Formatting Proxy for Tables
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
interface TableFormattingProxy extends FormattingProxy {
    public function getTableName(): string;
}