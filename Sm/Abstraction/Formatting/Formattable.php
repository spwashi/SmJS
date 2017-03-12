<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 4:57 PM
 */

namespace Sm\Abstraction\Formatting;


interface Formattable {
    /**
     * Return the variables that this class deems relevant to the formatter.
     *
     * @return array
     */
    public function getVariables(): array;
}