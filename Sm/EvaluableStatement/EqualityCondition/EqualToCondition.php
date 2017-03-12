<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:27 PM
 */

namespace Sm\EvaluableStatement\EqualityCondition {
    class EqualToCondition extends EqualityCondition_ {
        protected $_symbol_ = '=';
        
        public function getDefaultEvaluator(): callable {
            return function (EqualToCondition $vars) {
                return $vars->left_side === $vars->right_side;
            };
        }
    }
}

namespace Sm\EvaluableStatement\EqualityCondition\EqualToCondition {
    
    use Sm\EvaluableStatement\EqualityCondition\EqualToCondition;
    use Sm\EvaluableStatement\EvaluableStatementFactory;
    
    /**
     * Create the EqualToCondition as usual
     *
     * @param \Sm\EvaluableStatement\EvaluableStatementFactory $_
     * @param array                                            $args
     *
     * @return mixed|\Sm\EvaluableStatement\EvaluableStatement
     */
    function _(EvaluableStatementFactory $_, array $args) {
        return $_(EqualToCondition::class)->set(...$args);
    }
}