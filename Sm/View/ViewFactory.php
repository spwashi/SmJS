<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 1:00 AM
 */

namespace Sm\View;


use Sm\Factory\Factory;

class ViewFactory extends Factory {
    
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return View
     */
    public function build($operand = null) {
        if ($operand instanceof View) {
            return $operand;
        }
        if ($operand instanceof Viewable) {
            return $operand->toView();
        }
        return View::init($operand);
    }
}