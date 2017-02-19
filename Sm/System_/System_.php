<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:04 PM
 */

namespace Sm\System_;


use Sm\Factory\Factory;

class System_ {
    protected static $log_buffers = [];
    protected static $factories   = [];
    
    public static function reset_defaults() {
        if (file_exists(__DIR__ . '/_initialize.php')) {
            require __DIR__ . '/_initialize.php';
        }
    }
    public static function clear_defaults() {
        static::$log_buffers = [];
        static::$factories   = [];
    }
    /**
     * Register a Factory as being part of the system's defaults
     *
     * @param string              $root_class
     * @param \Sm\Factory\Factory $instance
     */
    public static function registerFactory(string $root_class, Factory $instance) {
        if (!isset(static::$factories[ $root_class ])) static::$factories[ $root_class ] = [];
        array_unshift(static::$factories[ $root_class ], $instance);
    }
    public static function Factory(string $root_class): Factory {
        /** @var Factory[] $Factories */
        $Factories = static::$factories[ $root_class ] ?? [];
        
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
            public function attempt_build() {
                return call_user_func_array($this->attempt_build_method, func_get_args());
            }
        };
        return $Factory;
    }
}

System_::reset_defaults();