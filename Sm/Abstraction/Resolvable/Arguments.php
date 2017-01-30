<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 8:46 PM
 */

namespace Sm\Abstraction\Resolvable;


class Arguments implements \JsonSerializable {
    public $arguments = [ ];
    public static function coerce($arguments) {
        if ($arguments instanceof Arguments) return $arguments;
        $arguments = is_array($arguments) ? $arguments : func_get_args();
        return new static($arguments);
    }
    
    public function setArguments($arguments) {
        $this->arguments = is_array($arguments) ? $arguments : func_get_args();
        return $this;
    }
    
    public function push($argument) {
        $this->arguments[] = $argument;
        return $this;
    }
    
    public function length() {
        return count($this->arguments);
    }
    
    public function __construct($arguments = null) {
        if ($arguments === null) {
            $this->arguments = [ ];
        } else if (!is_array($arguments)) {
            $this->arguments = func_get_args();
        } else {
            $this->arguments = $arguments;
        }
    }
    public function getListedArguments() {
        return ($this->arguments);
    }
    public function jsonSerialize() {
        return [
            'arguments' => $this->arguments,
        ];
    }
    
}