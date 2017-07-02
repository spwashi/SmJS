<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 10:13 PM
 */

namespace Sm\Core\Context;


/**
 * Class ContextDescriptor
 *
 * This class is meant to serve as an interface describing the features of a Context that we are looking for
 *
 * @package Sm\Core\Context
 */
class ContextDescriptor {
    /** @var null Classes that must match */
    protected $matching_context_classes = null;
    /**
     * Set an array of the classes that match this Context Descriptor
     *
     * @param array $matching_context_classes
     *
     * @return $this
     */
    public function setMatchingContextClasses(array $matching_context_classes) {
        $this->matching_context_classes = $matching_context_classes;
        
        return $this;
    }
}