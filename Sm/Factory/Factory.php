<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Factory;


use Sm\Resolvable\Error\UnresolvableError;

class Factory implements \Sm\Abstraction\Factory\Factory {
    protected $registry       = [];
    protected $class_registry = [];
    /**
     * Try to build something without returning a default
     *
     * @return null
     */
    public function attempt_build() {
        $args       = func_get_args();
        $class_name = $args[0] ?? null;
        if (is_string($class_name) && class_exists($class_name)) {
            array_shift($args);
            return $this->create_class($class_name);
        }
        foreach ($this->registry as $index => $method) {
            $result = $method(...$args);
            if ($result) return $result;
        }
        return null;
    }
    public function create_class(string $class_name) {
        $actual_class_name = $this->class_registry[ $class_name ] ?? null;
        if (is_object($actual_class_name)) return $actual_class_name;
        else if (is_string($actual_class_name)) $class_name = $actual_class_name;
        
        if (!class_exists($class_name)) throw new UnresolvableError("Class not found");
        
        $class = new $class_name;
        if (!isset($actual_class_name)) $this->register($class, $class_name);
        return $class;
    }
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @return null
     */
    public function build() {
        return $this->attempt_build(...func_get_args());
    }
    /**
     * @param      $item
     *
     * @param null $name
     *
     * @return $this
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function register($item, $name = null) {
        if ((is_string($item) && class_exists($item)) || (is_object($item) && !($item instanceof \Closure))) {
            $this->class_registry[ $name ] = $item;
        } else {
            if (!is_callable($item)) throw new UnresolvableError("Cannot register factory method");
            array_unshift($this->registry, $item);
        }
        return $this;
    }
}