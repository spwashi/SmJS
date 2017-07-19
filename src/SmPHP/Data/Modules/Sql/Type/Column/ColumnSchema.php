<?php
/**
 * User: Sam Washington
 * Date: 7/18/17
 * Time: 11:58 AM
 */

namespace Sm\Data\Modules\Sql\Type\Column;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Schema\Schema;

/**
 * Class ColumnSchema
 *
 * Meant to represent a Column
 *
 * @package Sm\Data\Modules\Sql\Type\Column
 */
abstract class ColumnSchema implements Schema {
    protected $name;
    /** @var  bool */
    protected $can_be_null = true;
    protected $type;
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
    public function setLength(int $length) {
        $this->length = $length;
        return $this;
    }
    public function canBeNull() { return $this->can_be_null; }
}