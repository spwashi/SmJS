<?php
/**
 * User: Sam Washington
 * Date: 3/25/17
 * Time: 3:40 PM
 */

namespace Sm\Process\EvaluableStatement\Constructs;


use Sm\Process\EvaluableStatement\EvaluableStatement;

/**
 * Class IsChainableConstructTrait
 *
 * Trait that has the common features of ChainableConstructs
 *
 * @see     \Sm\Process\EvaluableStatement\Constructs\ChainableConstruct
 *
 * @package Sm\Process\EvaluableStatement\Constructs
 */
trait ChainableConstructTrait {
    protected $_items_ = [];
    /**
     * Give us a way to set the variables after we've initialized the class
     *
     * @return mixed|\Sm\Process\EvaluableStatement\EvaluableStatement
     */
    public function set(): EvaluableStatement {
        $items = func_get_args();
        foreach ($items as $key => $item) {
            if (isset($item) || (!isset($this->_items_[ $key ]))) {
                $this->_items_[ $key ] = $item;
            }
        }
        return $this;
    }
}