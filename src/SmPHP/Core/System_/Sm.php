<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:04 PM
 */

namespace Sm\Core\System_;


use Sm\Core\Context\ResolutionContext;
use Sm\Core\Factory\Factory;
use Sm\Core\Factory\FactoryContainer;
use Sm\Core\Internal\Logging\LoggerFactory;

class Sm {
    /** @var  \Sm\Core\Context\ResolutionContext */
    protected static $ResolutionContext;
    
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
        return static::resolveFactory(LoggerFactory::class)->build(...$args);
    }
    
    public static function setDefaults() {
        if (file_exists(__DIR__ . '/_initialize.php')) {
            require __DIR__ . '/_initialize.php';
        }
    }
    /**
     * Register a Factory as being part of the system's defaults
     *
     * @param string                   $root_class
     * @param \Sm\Core\Factory\Factory $instance
     */
    public static function registerFactory(string $root_class, Factory $instance) {
        static::$ResolutionContext->Factories->register($root_class, $instance);
    }
    public static function resolveFactory(string $root_class): Factory {
        return static::$ResolutionContext->Factories->resolve($root_class);
    }
    
    /**
     * @param \Sm\Core\Factory\FactoryContainer $FactoryContainer
     */
    public static function setFactoryContainer(FactoryContainer $FactoryContainer) {
        static::$ResolutionContext->setFactoryContainer($FactoryContainer);
    }
    public static function setResolutionContext(ResolutionContext $ResolutionContext) {
        static::$ResolutionContext = $ResolutionContext;
    }
}

Sm::setDefaults();