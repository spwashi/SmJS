<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 1:11 PM
 */

namespace Sm\Process\EvaluableStatement\EqualityCondition;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Process\EvaluableStatement\EvaluableStatement;

/**
 * Class _EqualityCondition
 * Represents inequalities/equalities.
 *
 * @property-read string $left_side
 * @property-read string $symbol
 * @property-read string $right_side
 *
 * @method string|Formatter|mixed left_side(bool $do_format = true)
 * @method string|Formatter|mixed right_side(bool $do_format = true)
 * @method string|Formatter|mixed symbol(bool $do_format = true)
 *
 * @package Sm\Process\EvaluableStatement\EqualityCondition
 */
abstract class EqualityCondition_ extends EvaluableStatement {
    protected $_symbol_;
    protected $_left_side_  = null;
    protected $_right_side_ = null;
    public static function init($left_side = null, $right_side = null) {
        $new               = new static;
        $new->_left_side_  = $left_side;
        $new->_right_side_ = $right_side;
        return $new;
    }
    public function set($left_side = null, $right_side = null): EvaluableStatement {
        if (isset($left_side)) {
            $this->_left_side_ = $left_side;
        }
        if (isset($right_side)) {
            $this->_right_side_ = $right_side;
        }
        return $this;
    }
}