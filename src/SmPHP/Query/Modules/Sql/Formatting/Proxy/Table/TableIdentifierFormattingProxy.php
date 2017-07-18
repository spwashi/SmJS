<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 5:46 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Table;

use Sm\Query\Modules\Sql\Formatting\Proxy\Database\DatabaseFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;

/**
 * Class TableFormattingProxy
 *
 * Formatting Proxy for Tables
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
abstract class TableIdentifierFormattingProxy extends SqlFormattingProxy {
    protected $table_name;
    /** @var  DatabaseFormattingProxy */
    protected $database;
    abstract public function getTableName(): string;
    abstract public function getDatabase():? DatabaseFormattingProxy;
}