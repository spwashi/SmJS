<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 10:27 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Table;


use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;
use Sm\Storage\Database\Table\TableSource;

class TableNameFormattingProxy extends SqlFormattingProxy implements TableFormattingProxy {
    public function getName(): string {
        if (is_string($this->subject)) return $this->subject;
        if ($this->subject instanceof TableSource) return $this->subject->getName();
        throw new UnimplementedError("Cannot format things as Table Names when they aren't TableSources or Strings");
    }
    
}