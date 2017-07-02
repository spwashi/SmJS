<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:36 AM
 */

namespace Sm\Core\Factory;


use Sm\Core\Container\StandardContainer;
use Sm\Core\Exception\ClassNotFoundException;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Core\Factory\Exception\WrongFactoryException;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Util;

/**
 * Class AbstractFactory
 *
 * Generic implementation of Factory Interface
 *
 * @package Sm\Core\Factory
 */
abstract class StandardFactory extends StandardContainer implements Factory {
    /** Mode of creating factories: Create classes that aren't registered (as long as it's okay to) */
    const MODE_DO_CREATE_MISSING = 'do_create_missing';
    
    /** @var  \Sm\Core\Container\Mini\MiniCache $Cache */
    protected $Cache;
    /** @var Resolvable[] */
    protected $registry = [];
    /** @var array $class_registry */
    protected $class_registry = [];
    /** @var bool If there is a class that isn't registered in the factory (and doesn't have ancestor that is), should we create it anyways? */
    protected $do_create_missing = true;
    
    /**
     * Set the Mode of Creating Factories
     *
     * @param string $mode
     *
     * @return $this
     */
    public function setCreationMode($mode = StandardFactory::MODE_DO_CREATE_MISSING) {
        $this->do_create_missing = $mode === StandardFactory::MODE_DO_CREATE_MISSING;
        return $this;
    }
    public function resolve($name = null) {
        return $this->build(...func_get_args());
    }
    public function build() {
        $args   = func_get_args();
        $result = $this->Cache->resolve($args);
        
        # If we've already built the item and have it cached, return it
        if (isset($result)) {
            return $result;
        }
        
        # Try to build the item
        $result = $this->attempt_build(...$args);
        
        
        # Cache the result if we've decided that's necessary
        $this->Cache->cache($args, $result);
        return $result;
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
     * Try to build something without returning a default
     *
     * @return mixed
     * @throws \Sm\Core\Factory\Exception\FactoryCannotBuildException If we can't build the item
     */
    protected function attempt_build() {
        $args = func_get_args();
        /** @var string $class_name */
        $class_name = $args[0] ?? null;
        $class_name = is_object($class_name) ? get_class($class_name) : $class_name;
        
        $previous_exception = null;
        if (is_string($class_name) || ($class_name = gettype($class_name)) && isset($this->class_registry[ $class_name ])
        ) {
            
            try {
                # If the original class exists or we found a match, create the class
                return $this->buildClassInstance($class_name, $args);
            } catch (ClassNotFoundException $e) {
                $previous_exception = $e;
            }
        }
        
        
        # Iterate through the other registry to see if there is some sort of different check
        #  being done
        /**
         * @var                    $index
         * @var Resolvable         $method
         */
        foreach ($this->registry as $index => $method) {
            $result = $method->resolve(...$args);
            if ($result) {
                return $result;
            }
        }
        throw new FactoryCannotBuildException("Cannot find a matching build method", null, $previous_exception);
    }
    /**
     * Build a class relevant to this Factory
     *
     * @param string $class_name
     *
     * @param array  $args
     *
     * @return mixed|null
     * @throws \Sm\Core\Factory\Exception\WrongFactoryException
     * @throws \Sm\Core\Resolvable\Error\UnresolvableException
     */
    protected function buildClassInstance(string $class_name, array $args = []) {
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
        
        # If the class name is an object, clone it
        if (is_object($class_handler)) {
            $this->_checkCanInit(get_class($class_handler));
            return clone $class_handler;
        } else {
            $this->_checkCanInit($class_handler);
        }
        
        
        if (!class_exists($class_name)) {
            throw new ClassNotFoundException("Class {$class_name} not found");
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
     * @return null|\Sm\Core\Resolvable\Resolvable
     */
    protected function standardizeRegistrand($registrand):? Resolvable {
        return is_callable($registrand) ? FunctionResolvable::init($registrand) : NativeResolvable::init($registrand);
    }
    private function _checkCanInit($class_name) {
        if (!$this->canCreateClass($class_name) || !$this->do_create_missing) {
            throw new WrongFactoryException("Not allowed to create class of type {$class_name}");
        }
    }
}