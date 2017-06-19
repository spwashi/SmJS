<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 2:21 PM
 */

namespace Sm\Process\EvaluableStatement\Constructs;

use Sm\Process\EvaluableStatement\DeferredEvaluationStatement;
use Sm\Process\EvaluableStatement\EvaluableStatement;

/**
 * Class Or_
 *
 * Represents the "Or" construct. Returns true only if all items return true
 *
 * @package Sm\Process\EvaluableStatement\Constructs
 */
class Or_ extends EvaluableStatement implements ChainableConstruct {
    use ChainableBooleanConstruct;
    protected $_construct_ = 'Or';
    /**
     * Method called in the constructor that returns the default function to use to evaluate the EvaluableStatement
     *
     * @return mixed
     */
    protected function getDefaultEvaluator(): callable {
        return function () {
            # Iterate through the items to see if one of the statements evaluate to true
            foreach ($this->_items_ as $item) {
                $item = $this->valueOf($item);
                if ($item instanceof DeferredEvaluationStatement) {
                    return new DeferredEvaluationStatement($this);
                } else if ($item) {
                    return true;
                }
            }
            return false;
        };
    }
}