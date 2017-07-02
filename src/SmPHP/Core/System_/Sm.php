<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:04 PM
 */

namespace Sm\Core\System_;


use Sm\Core\Context\ResolutionContext;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Factory\Factory;

class Sm {
    /** @var  \Sm\Core\Context\ResolutionContext */
    protected static $ResolutionContext;
    
    public static function setDefaults() {
        if (file_exists(__DIR__ . '/_initialize.php')) {
            require __DIR__ . '/_initialize.php';
        }
    }
    /**
     * Register a Factory as being part of the system's defaults
     *
     * @param string  $root_class
     * @param Factory $instance
     */
    public static function registerFactory(string $root_class, Factory $instance) {
        static::$ResolutionContext->Factories->register($root_class, $instance);
    }
    public static function resolveFactory(string $root_class): Factory {
        throw new UnimplementedError("Still thinking about this one");
    }
    
    public static function setResolutionContext(ResolutionContext $ResolutionContext) {
        static::$ResolutionContext = $ResolutionContext;
    }
}

Sm::setDefaults();