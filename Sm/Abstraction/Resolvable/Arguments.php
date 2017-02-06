<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 8:46 PM
 */

namespace Sm\Abstraction\Resolvable;


class Arguments implements \JsonSerializable {
    public $arguments  = [ ];
    public $parameters = [ ];
    public function __construct($arguments = null) {
        if ($arguments === null) {
            $this->arguments = [ ];
        } else if (!is_array($arguments)) {
            $this->arguments = func_get_args();
        } else {
            $this->arguments = $arguments;
        }
    }
    public function setArguments($arguments) {
        $this->arguments = is_array($arguments) ? $arguments : func_get_args();
        return $this;
    }
    
    public function setParameter($name, $argument) {
        $this->parameters[ $name ] = $argument;
        return $this;
    }
    public function getParameter($name) {
        return $this->parameters[ $name ] ?? null;
    }
    public function getArgument($index) {
        return $this->arguments[ $index ] ?? null;
    }
    public function shift() {
        if (count($this->arguments)) return array_shift($this->arguments);
        return null;
    }
    public function unshift($argument, $name = null) {
        array_unshift($this->arguments, $argument);
        return isset($name) ? $this->setParameter($name, $argument) : $this;
    }
    public function push($argument, $name = null) {
        $this->arguments[] = $argument;
        return isset($name) ? $this->setParameter($name, $argument) : $this;
    }
    
    public function length() {
        return count($this->arguments);
    }
    public function _list() {
        return $this->arguments;
    }
    public function getListedArguments() {
        return ($this->arguments);
    }
    public function jsonSerialize() {
        return [
            'arguments'  => $this->arguments,
            'parameters' => count($this->parameters) ? $this->parameters : null,
        ];
    }
    public static function coerce($arguments = null, $fallback = null) {
        if ($arguments instanceof Arguments) return $arguments;
        $arguments = is_array($arguments) ? $arguments : (is_array($fallback) ? $fallback : null);
        return new static($arguments);
    }
    
}