<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:14 PM
 */

namespace Sm\EvaluableStatement\EqualityCondition;

class GreaterThanCondition extends EqualityCondition_ {
    protected $_symbol_ = '>';
    public function getDefaultEvaluator(): callable {
        return function ($vars) {
            if (is_scalar($vars->left_side) && is_scalar($vars->right_side)) {
                return $vars->left_side > $vars->right_side;
            }
            return null;
        };
    }
}