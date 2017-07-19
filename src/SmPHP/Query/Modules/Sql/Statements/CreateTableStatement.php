<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:34 AM
 */

namespace Sm\Query\Modules\Sql\Statements;


use Sm\Query\Statements\QueryComponent;

/**
 * Class CreateTableStatement
 *
 * QueryComponent to Create a Table
 *
 * @package Sm\Query\Modules\Sql\Statements
 */
class CreateTableStatement extends QueryComponent {
    protected $name;
    protected $columns = [];
    public function __construct($name, ...$columns) {
        $this->name = $name;
        $this->withColumns(...$columns);
    }
    public function withName(string $name) {
        $this->name = $name;
        return $this;
    }
    public function withColumns(...$columns) {
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }
    public function getColumns(): array {
        return $this->columns;
    }
    public function getName() { return $this->name; }
}