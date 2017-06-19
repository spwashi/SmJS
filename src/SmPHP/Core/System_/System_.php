<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:04 PM
 */

namespace Sm\Core\System_;


use Sm\Core\Factory\Factory;
use Sm\Core\Factory\FactoryContainer;
use Sm\Core\Internal\Logging\LoggerFactory;

class System_ {
    protected static $log_buffers = [];
    protected static $factories   = [];
    /** @var  \Sm\Core\Factory\FactoryContainer $FactoryContainer */
    protected static $FactoryContainer;
    
    /**
     * Logging capabilities for the whole system.
     *
     * @return \Monolog\Logger
     */
    public static function Log() {
        $args = func_get_args();
        if (count($args)) {
            array_unshift($args, 'System');
        }
        return static::Factory(LoggerFactory::class)->build(...$args);
    }
    
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
     * @param string                   $root_class
     * @param \Sm\Core\Factory\Factory $instance
     */
    public static function registerFactory(string $root_class, Factory $instance) {
        static::$FactoryContainer->register($root_class, $instance);
    }
    
    public static function Factory(string $root_class): Factory {
        return static::$FactoryContainer->resolve($root_class);
    }
    /**
     * @param \Sm\Core\Factory\FactoryContainer $FactoryContainer
     */
    public static function setFactoryContainer(FactoryContainer $FactoryContainer) {
        self::$FactoryContainer = $FactoryContainer;
    }
}

System_::setFactoryContainer(new FactoryContainer);
System_::reset_defaults();