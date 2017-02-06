<?php
/**
 * User: spwashi2
 * Date: 1/26/2017
 * Time: 12:38 PM
 */

namespace Sm\Abstraction\Resolvable;


interface  Resolvable {
    /**
     * Return the final result of the Resolvable (as of now)
     *
     * @return mixed
     */
    public function resolve();
}