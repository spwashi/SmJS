<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:27 PM
 */

namespace Sm\Condition\EqualityCondition;


class EqualToCondition extends _EqualityCondition {
    protected $symbol = '=';
    public function getDefaultEvaluator(): callable {
        return function () { return $this->left_side === $this->right_side; };
    }
}