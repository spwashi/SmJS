<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 8:29 PM
 */

namespace Sm\Abstraction\Factory;


interface Factory {
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return mixed
     */
    public function build($operand);
}