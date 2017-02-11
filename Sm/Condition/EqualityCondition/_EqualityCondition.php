<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:11 PM
 */

namespace Sm\Condition\EqualityCondition;


use Sm\Condition\Condition;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Util;

abstract class _EqualityCondition extends Condition {
    protected $symbol;
    protected $left_side  = null;
    protected $right_side = null;
    
    public static function init($left_side = null, $right_side = null) {
        $new = new static;
        
        if (!$new->canCompare($left_side, $right_side)) throw new UnresolvableError("Cannot compare the two provided elements");
        
        $new->left_side  = $left_side;
        $new->right_side = $right_side;
        
        return $new;
    }
    public static function coerce($left_side = null, $right_side = null) {
        return static::init($left_side, $right_side);
    }
    
    /**
     * Function to see if two values are comparable
     *
     * @param $left
     * @param $right
     *
     * @return bool
     */
    protected function canCompare($left, $right): bool {
        if (is_numeric($left) && is_numeric($right)) return true;
        
        else if (is_string($left) && Util::canBeString($right)) return true;
        
        else if (false) return false;
        
        else return false;
    }
}