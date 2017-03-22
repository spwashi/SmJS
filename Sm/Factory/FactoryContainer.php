<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 12:50 AM
 */

namespace Sm\Factory;


use Sm\Container\Container;

/**
 * Class FactoryContainer
 * Class that contains all of the Factories that a given operation might use.
 *
 * @package Sm\Factory
 */
class FactoryContainer extends Container {
    public function register($name = null, $registrand = null, $overwrite = false) {
        # This class stores factories as an array of arrays
        $this->registry[ $name ]   = !$overwrite ? $this->registry[ $name ] ??[] : [];
        $this->registry[ $name ][] = $registrand;
        return $this;
    }
    public function resolve($name = null) {
        /** @var Factory[] $Factories */
        $Factories      = $this->_search_registry_for_name($name);
        $_factory_count = count($Factories);
        if ($_factory_count === 1) return $Factories[0];
        else if ($_factory_count < 2 && class_exists($name)) {
            $_class = new $name;
            if ($_class instanceof Factory) return new $name;
        }
    
        return $this->createFactory($Factories);
    }
    /**
     * Function that allows us to take advantage of an assumed distinction Factory names.
     * This function allows us to refer to a class with or without its namespace as long as
     * it's been registered here already
     *
     * @param $name
     *
     * @return array
     */
    private function _search_registry_for_name($name) {
        if (isset($this->registry[ $name ])) return $this->registry[ $name ];
        else if (class_exists($name)) return [];
        
        foreach ($this->registry as $index => $item) {
            if (($exp = explode('\\', $index)) && end($exp) && $exp[ key($exp) ] === $name) {
                return $item;
            }
        }
        
        
        return [];
    }
    /**
     * Create a factory on the fly that emulates the behavior of one Factory - useful if there is an array
     * being registered.
     *
     * @param $Factories
     *
     * @return \Sm\Factory\Factory
     */
    private function createFactory($Factories): Factory {
        /**
         * Method to either return the completed Object or to return null;
         *
         * @return null
         */
        $attempt_build_method = function () use ($Factories) {
            foreach ($Factories as $LastFactory) {
                $result = $LastFactory->attempt_build(...func_get_args());
                if ($result) return $result;
            }
            return null;
        };
        
        /**
         * Function to try to build something
         *
         * @return null
         */
        $build_method = function () use ($Factories, $attempt_build_method) {
            $result = $attempt_build_method(...func_get_args());
            if ($result) return $result;
            $LastFactory = $Factories[ count($Factories) - 1 ] ?? null;
            return $LastFactory ? $LastFactory->build(...func_get_args()) : null;
        };
        
        /** @var Factory $Factory */
        $Factory = new class($build_method, $attempt_build_method) extends Factory {
            protected $build_method         = null;
            protected $attempt_build_method = null;
            public function __construct($build_method, $attempt_build_method) {
                $this->build_method         = $build_method;
                $this->attempt_build_method = $attempt_build_method;
            }
            public function build() {
                return call_user_func_array($this->build_method, func_get_args());
            }
            public function canCreateClass($item) {
                return class_exists($item);
            }
            public function attempt_build() {
                return call_user_func_array($this->attempt_build_method, func_get_args());
            }
        };
        return $Factory;
    }
}