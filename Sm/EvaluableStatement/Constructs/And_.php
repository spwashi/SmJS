<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 2:21 PM
 */

namespace Sm\EvaluableStatement\Constructs;

use Sm\EvaluableStatement\EvaluableStatement;

/**
 * Class And_
 *
 * Represents the "AND" construct. Returns true only if all items return true
 *
 * @package Sm\EvaluableStatement\Constructs
 */
class And_ extends EvaluableStatement implements ChainableConstruct {
    use ChainableBooleanConstruct;
    protected $_items_;
    protected $_construct_ = 'and';
    /**
     * Give us a way to set the variables after we've initialized the class
     *
     * @return mixed|\Sm\EvaluableStatement\EvaluableStatement
     */
    public function set(): EvaluableStatement {
        $items         = func_get_args();
        $this->_items_ = $items;
        return $this;
    }
    /**
     * Method called in the constructor that returns the default function to use to evaluate the EvaluableStatement
     *
     * @return mixed
     */
    protected function getDefaultEvaluator(): callable {
        return function () {
            # Iterate through the items to see if all of the statements evaluate to true
            foreach ($this->_items_ as $item) {
                if (!$this->valueOf($item)) return false;
            }
            return true;
        };
    }
}