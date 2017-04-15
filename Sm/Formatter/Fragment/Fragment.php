<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 4:16 PM
 */

namespace Sm\Formatter\Fragment;


use Sm\Abstraction\Formatting\Formattable;

abstract class Fragment implements Formattable {
    public function setVariables(array $variables) {
        foreach ($variables as $index => $variable) {
            $this->$index = $variable;
        }
        return $this;
    }
    public static function init() {
        return new static;
    }
    /**
     * Given one Fragment, create another of this class type using its variables.
     *
     * @param Fragment $Fragment
     *
     * @return $this
     */
    public static function inherit(Fragment $Fragment) {
        return static::init()->setVariables($Fragment->getVariables());
    }
}