<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 8:45 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing;


use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\Proxy\Database\DatabaseFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableFormattingProxy;

/**
 * Class TableAliasProxy
 *
 * Represents an Aliased Table
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing
 * @method  TableAliasProxy static init(...$items)
 */
class TableAliasProxy implements TableFormattingProxy {
    protected $table;
    protected $alias;
    public function __construct(TableFormattingProxy $table, $alias) {
        $this->table = $table;
        if (!is_string($alias)) throw new UnimplementedError("+ Anything but string aliases");
        $this->alias = $alias;
    }
    public function getTableName(): string {
        return $this->alias;
    }
    public function getDatabase(): ?DatabaseFormattingProxy {
        return $this->table->getDatabase();
    }
}