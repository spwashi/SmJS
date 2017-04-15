<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 9:07 PM
 */

namespace Sm\Resolvable;


use Sm\Resolvable\Error\UnresolvableError;

/**
 * Class ArrayResolvable
 *
 * Resolvable that references arrays, or ultimately resolves to a array
 *
 * @package Sm\Resolvable
 */
class ArrayResolvable extends NativeResolvable implements \JsonSerializable {
    /** @var */
    protected $subject;
    public function __construct($subject = null) {
        if (!is_array($subject)) {
            throw new UnresolvableError("Could not resolve subject");
        }
        parent::__construct($subject);
    }
    /**
     * @return array
     */
    public function resolve() {
        return parent::resolve() ?? [];
    }
    
    function jsonSerialize() {
        return $this->subject;
    }
}