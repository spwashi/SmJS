<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:58 AM
 */

namespace Sm\Query\Modules\Sql\Type\Column;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schema;

/**
 * Class ColumnSchema
 *
 * Meant to represent a Column
 *
 * @package Sm\Query\Modules\Sql\Type\Column
 */
abstract class ColumnSchema implements Schema {
    protected $name;
    /** @var  bool */
    protected $can_be_null = true;
    protected $type;
    protected $unique      = false;
    protected $length;
    
    public function __construct(string $name = null) {
        if ($name) $this->setName($name);
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function compare($item) {
        throw new UnimplementedError("+ Cannot compare to Column Schemas");
    }
    public function getName(): ?string {
        return $this->name;
    }
    /**
     * @param $name
     *
     * @return \Sm\Query\Modules\Sql\Type\Column\ColumnSchema
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function getType():?string {
        return $this->type;
    }
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    public function getLength():?int {
        return $this->length;
    }
    /**
     * @param int $length
     *
     * @return \Sm\Query\Modules\Sql\Type\Column\ColumnSchema
     */
    public function setLength(int $length) {
        $this->length = $length;
        return $this;
    }
    /**
     * @param bool $nullability
     *
     * @return \Sm\Query\Modules\Sql\Type\Column\ColumnSchema
     */
    public function setNullability($nullability = false) {
        $this->can_be_null = (bool)$nullability;
        return $this;
    }
    public function canBeNull() { return $this->can_be_null; }
    public function isUnique(): bool {
        return $this->unique;
    }
    /**
     * @param bool $unique
     *
     * @return ColumnSchema
     */
    public function setUnique(bool $unique) {
        $this->unique = $unique;
        return $this;
    }
}