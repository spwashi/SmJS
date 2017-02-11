<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:28 PM
 */

namespace Sm\Condition;


use Sm\Abstraction\Formatting\Formattable;
use Sm\Resolvable\Resolvable;

abstract class Condition extends Resolvable implements Formattable {
    /**
     * Return the final result of the Resolvable (as of now)
     *
     * @return mixed
     */
    public function resolve() {
        return null;
    }
}