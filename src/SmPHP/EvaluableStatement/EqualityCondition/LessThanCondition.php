<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:14 PM
 */

namespace Sm\EvaluableStatement\EqualityCondition;
class LessThanCondition extends EqualityCondition_ {
    protected $_symbol_ = '<';
    public function getDefaultEvaluator(): callable {
        return function ($vars) {
            return $vars->left_side < $vars->right_side;
        };
    }
}