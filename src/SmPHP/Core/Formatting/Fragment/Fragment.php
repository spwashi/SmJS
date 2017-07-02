<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 4:16 PM
 */

namespace Sm\Core\Formatting\Fragment;

/**
 * Class Fragment
 *
 * This is a class that represents a specific combination of a set of variables within a context determined by the Fragment.
 *
 * Useful in situations where we know specifically what kind of information is represented, but we want to keep that
 * separate from the way it gets formatted.
 *
 * @package Sm\Core\Formatter\Fragment
 */
abstract class Fragment {
    /**
     * Static constructor for Fragments
     *
     * @return static
     */
    public static function init() {
        return new static;
    }
    /**
     * Get an array of the attributes represented by this Fragment that we want to get formatted
     *
     * @return array
     */
    public function getFormattedAttributes(): array {
        return get_object_vars($this);
    }
    /**
     * Set an array of the attributes represented by this Fragment that we want to get formatted
     *
     * @param array $variables
     *
     * @return $this
     */
    public function setFormattedAttributes(array $variables) {
        foreach ($variables as $index => $variable) {
            $this->$index = $variable;
        }
        return $this;
    }
    /**
     * Given one Fragment, create another of this class type using its variables.
     *
     * @param Fragment $Fragment
     *
     * @return $this
     */
    public static function inherit(Fragment $Fragment) {
        return static::init()->setFormattedAttributes($Fragment->getFormattedAttributes());
    }
}