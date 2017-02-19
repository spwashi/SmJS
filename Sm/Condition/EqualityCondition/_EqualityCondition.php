<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:11 PM
 */

namespace Sm\Condition\EqualityCondition;


use Sm\Condition\Condition;

abstract class _EqualityCondition extends Condition {
    protected $symbol;
    protected $left_side  = null;
    protected $right_side = null;
    
    public function getVariables(): array {
        return [
            'left_side'  => $this->left_side,
            'right_side' => $this->right_side,
            'symbol'     => $this->symbol,
        ];
    }
    public function resolve() {
        $this->left_side  = $this->valueOf($this->left_side);
        $this->right_side = $this->valueOf($this->right_side);
        return parent::resolve();
    }
    public static function init($left_side = null, $right_side = null) {
        $new             = new static;
        $new->left_side  = $left_side;
        $new->right_side = $right_side;
        return $new;
    }
    public static function coerce($left_side = null, $right_side = null) {
        return static::init($left_side, $right_side);
    }
}