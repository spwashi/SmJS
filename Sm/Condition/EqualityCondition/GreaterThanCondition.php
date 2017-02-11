<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:14 PM
 */

namespace Sm\Condition\EqualityCondition;


class GreaterThanCondition extends _EqualityCondition {
    protected $symbol = '>';
    public function resolve() {
        if (!$this->canCompare($this->left_side, $this->right_side)) return false;
        
        if (is_scalar($this->left_side) && is_scalar($this->right_side) && $this->left_side > $this->right_side) return true;
        
        return false;
    }
    
}