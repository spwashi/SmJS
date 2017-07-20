<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 9:33 PM
 */

namespace Sm\Query\Modules\Sql\Constraints;


use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Type\Column\ColumnSchema;

class PrimaryKeyConstraintSchema implements KeyConstraintSchema {
    /** @var \Sm\Query\Modules\Sql\Type\Column\ColumnSchema[] */
    private $columns;
    public function __construct(ColumnSchema ...$columns) {
        $this->columns = $columns;
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function compare($item) {
        throw new UnimplementedError("+ Cannot compare to PrimaryKeyConstraint Schemas");
    }
    /**
     * @return \Sm\Query\Modules\Sql\Type\Column\ColumnSchema[]
     */
    public function getColumns(): array {
        return $this->columns;
    }
    public function addColumn(ColumnSchema $columnSchema) {
        $this->columns[] = $columnSchema;
        return $this;
    }
}