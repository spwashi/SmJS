<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:14 PM
 */

namespace Sm\Condition\EqualityCondition;


class GreaterThanCondition extends _EqualityCondition {
    protected $symbol = '>';
    public function getDefaultEvaluator(): callable {
        return function () { return $this->left_side > $this->right_side; };
    }
}