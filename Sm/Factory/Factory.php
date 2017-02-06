<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Factory;


abstract class Factory implements \Sm\Abstraction\Factory\Factory {
    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return mixed
     */
    abstract public function build($operand);
}