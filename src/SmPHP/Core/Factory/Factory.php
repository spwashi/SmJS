<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Container\AbstractContainer;
use Sm\Core\Resolvable\Error\UnresolvableError;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

/**
 * Class Factory
 *
 * @package Sm\Core\Factory
 * @property-read \Sm\Core\Container\Mini\MiniCache $Cache
 */
class Factory extends AbstractContainer {
    /** @var  \Sm\Core\Container\Mini\MiniCache $Cache */
    protected $Cache;
    protected $cache_key;
    
    /** @var Resolvable[] */
    protected $registry = [];
    /** @var array $class_registry */
    protected $class_registry = [];
    /** @var bool If there is a class that isn't registered in the factory (and doesn't have ancestor that is), should we create it anyways? */
    protected $do_create_missing = true;
    
    
    /**
     * @return mixed
     */
    function __invoke() {
        $result = $this->build(...func_get_args());
        return $result;
    }
    
    public function doNotCreateMissing() {
        $this->do_create_missing = false;
        return $this;
    }
    public function doCreateMissing() {
        $this->do_create_missing = true;
        return $this;
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
        $class_name = is_object($class_name) ? get_class($class_name) : $class_name;
    
        if (is_string($class_name) || ($class_name = gettype($class_name)) &&
                                      isset($this->class_registry[ $class_name ])
        ) {
            # If the original class exists or we found a match, create the class
            try {
                return $this->create_class($class_name, $args);
            } catch (WrongFactoryException $E) {
            } catch (UnresolvableError $E) {
            }
        }
    
    
        # Iterate through the other registry to see if there is some sort of different check
        #  being done
        /**
         * @var            $index
         * @var Resolvable $method
         */
        foreach ($this->registry as $index => $method) {
            $result = $method->resolve(...$args);
            if ($result) {
                return $result;
            }
        }
        return null;
    }
    /**
     * @param null $name
     *
     * @return mixed|null
     */
    
    public function resolve($name = null) {
        $args   = func_get_args();
        $result = $this->Cache->resolve($args);
        
        if (isset($result)) {
            return $result;
        }
        $result = $this->attempt_build(...$args);
        
        
        # Cache the result if we've decided that's necessary
        $this->Cache->cache($args, $result);
        return $result;
    }
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @return null
     */
    public function build() {
        return static::resolve(...func_get_args());
    }
    /**
     * Register a method to use to build this factory
     *
     * @param null $name
     *
     * @param      $registrand
     *
     * @return $this
     */
    public function register($name = null, $registrand = null) {
        if (is_array($registrand)) {
            foreach ($registrand as $key => $value) {
                $this->register(is_numeric($key) ? null : $key, $value);
            }
            return $this;
        } else {
            $registrand = $this->standardizeRegistrand($registrand);
            
            
            # If the "name" is an object, just use the classname
            if (is_object($name)) {
                $name = get_class($name);
            }
            if (is_string($name)) {
                $this->class_registry[ $name ] = $registrand;
            }
            
            # register functions that don't have a name
            #  or FunctionResolvables that don't have an
            if (!$name) {
                array_unshift($this->registry, $registrand);
            }
        }
        return $this;
    }
    /**
     * Build a class relevant to this Factory
     *
     * @param string $class_name
     *
     * @param array  $args
     *
     * @return mixed|null
     * @throws \Sm\Core\Factory\WrongFactoryException
     * @throws \Sm\Core\Resolvable\Error\UnresolvableError
     */
    protected function create_class(string $class_name, array $args = []) {
        # If there is a function to help us create the class, call that function with the original class name that we
        #  are trying to create
        $class_handler = Util::getItemByClassAncestry($class_name, $this->class_registry);
        
        # If we are resolving a function, return that result.
        if ($class_handler instanceof FunctionResolvable) {
            return $class_handler(...$args);
        }
        
        # Otherwise, set the class handler to be whatever the classhandler resolves to
        if ($class_handler instanceof Resolvable) {
            $class_handler = $class_handler->resolve(...$args);
        }
        
        # If the registry holds a string, use the string as a class name
        if (is_string($class_handler)) {
            $class_name = $class_handler;
        } # If the class name is an object, clone it
        else if (is_object($class_handler)) {
            return clone $class_handler;
        }
        
        
        if (!$this->canCreateClass($class_name) || !$this->do_create_missing) {
            throw new WrongFactoryException("Not allowed to create class of type {$class_name}");
        }
        
        if (!class_exists($class_name)) {
            throw new UnresolvableError("Class {$class_name} not found");
        }
        $class = new $class_name(...$args);
        return $class;
    }
    /**
     * Are we allowed to create factories of this class type?
     *
     * @param string|object $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return true;
    }
    /**
     * @param mixed $registrand Whatever is being registered
     *
     * @return null|\Sm\Core\Abstraction\Resolvable\Resolvable
     */
    protected function standardizeRegistrand($registrand) {
        return is_callable($registrand) ? FunctionResolvable::coerce($registrand) : NativeResolvable::coerce($registrand);
    }
}