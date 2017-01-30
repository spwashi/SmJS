<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Factory;


use Sm\Error\UnimplementedError;

abstract class Factory implements \Sm\Abstraction\Factory\Factory {
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return mixed
     */
    public function build($operand) {
        throw new UnimplementedError("Incomplete definition of a Factory");
    }
    public function register($callback, $name = null) {
        throw new UnimplementedError("Incomplete definition of a Factory");
    }
}