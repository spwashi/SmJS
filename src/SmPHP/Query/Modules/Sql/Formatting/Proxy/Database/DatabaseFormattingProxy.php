<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 9:34 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Database;


use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;

/**
 * Class DatabaseFormattingProxy
 *
 * FormattingProxies for Databases
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\
 */
abstract class DatabaseFormattingProxy extends SqlFormattingProxy {
    abstract public function getDatabaseName();
}