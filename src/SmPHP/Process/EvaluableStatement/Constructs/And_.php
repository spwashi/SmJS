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
 * Class And_
 *
 * Represents the "AND" construct. Returns true only if all items return true
 *
 * @package Sm\Process\EvaluableStatement\Constructs
 */
class And_ extends EvaluableStatement implements ChainableConstruct {
    use ChainableBooleanConstruct;
    protected $_construct_ = 'and';
    
    /**
     * Method called in the constructor that returns the default function to use to evaluate the EvaluableStatement
     *
     * @return mixed
     */
    protected function getDefaultEvaluator(): callable {
        return function () {
            $return_value = true;
            # Iterate through the items to see if all of the statements evaluate to true
            foreach ($this->_items_ as $item) {
                if (!($item = $this->valueOf($item))) {
                    return false;
                }
                if ($item instanceof DeferredEvaluationStatement) {
                    $return_value = DeferredEvaluationStatement::init($this);
                }
            }
            return $return_value;
        };
    }
}