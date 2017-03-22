<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Factory;


use Sm\Resolvable\Error\UnresolvableError;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\Resolvable;
use Sm\Util;

class Factory implements \Sm\Abstraction\Factory\Factory {
    protected $registry = [];
    /** @var array $class_registry */
    protected $class_registry = [];
    /**
     * @return mixed
     */
    function __invoke() {
        $result = $this->build(...func_get_args());
        return $result;
    }
    
    /**
     * Try to build something without returning a default
     *
     * @return null
     */
    public function attempt_build() {
        $args = func_get_args();
    
        /** @var string $class_name */
        $class_name = $args[0] ?? null;
    
        if (is_string($class_name)
            || is_object($class_name) && ($class_name = get_class($class_name))
            || isset($class_name) && ($class_name = gettype($class_name)) && isset($this->class_registry[ $class_name ])
        ) {
            # If the original class exists or we found a match, create the class
            try {
                return $this->create_class($class_name, $args);
            } catch (WrongFactoryException $E) {
            }
        }
    
    
        # Iterate through the other registry to see if there is some sort of different check
        #  being done
        foreach ($this->registry as $index => $method) {
            $result = $method(...$args);
            if ($result) return $result;
        }
        return null;
    }
    public function getHandlerFromRegistry($item) {
        if (!class_exists($item)) return $this->class_registry[ $item ] ?? null;
        $ancestors = Util::getAncestorClasses($item, true);
        foreach ($ancestors as $class_name) {
            if (isset($this->class_registry[ $class_name ])) return $this->class_registry[ $class_name ];
        }
        return null;
    }
    /**
     * Build a class relevant to this Factory
     *
     * @param string $class_name
     *
     * @param array  $args
     *
     * @return mixed|null
     * @throws \Sm\Factory\WrongFactoryException
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function create_class(string $class_name, array $args = []) {
        $actual_class_name = $this->getHandlerFromRegistry($class_name);
        
        # If there is a function to help us create the class, call that function with the original class name that we
        #  are trying to create
        if (is_callable($actual_class_name) && ($actual_class_name instanceof FunctionResolvable || !($actual_class_name instanceof Resolvable))) {
            return $actual_class_name(...$args);
        } # If the registry holds a string, use the string as a class name
        elseif (is_string($actual_class_name)) {
            $class_name = $actual_class_name;
        } # If the class name is an object, clone it
        else if (is_object($actual_class_name)) {
            return clone $actual_class_name;
        }
        
        
        if (!$this->canCreateClass($class_name)) {
            throw new WrongFactoryException("Not allowed to create class of type {$class_name}");
        }
        
        if (!class_exists($class_name)) throw new UnresolvableError("Class {$class_name} not found");
        
        $class = new $class_name(...$args);
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
     * Register a method to use to build this factory
     *
     * @param      $item
     *
     * @param null $name
     *
     * @return $this
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function register($item, $name = null) {
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $this->register($value, is_numeric($key) ? null : $key);
            }
            return $this;
        } else {
            if (is_string($name)) {
                $this->class_registry[ $name ] = $item;
            }
            # register functions that don't have a name
            #  or FunctionResolvables that don't have an
            if (is_callable($item) && (!$name)) {
                array_unshift($this->registry, $item);
            }
        }
        return $this;
    }
    /**
     * Are we allowed to create factories of this class type?
     *
     * @param string|object $object_type
     *
     * @return bool
     */
    public function canCreateClass($object_type) {
        return true;
    }
}