<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:27 AM
 */

namespace Sm\App;


use Sm\App\Module\Module;
use Sm\IoC\IoC;
use Sm\Resolvable\NativeResolvable;

/**
 * Class PathContainer
 *
 * @package Sm\App
 * @property string $base_path
 * @property string $config_path
 * @property string $app_path
 */
class PathContainer extends IoC {
    public $App = null;
    /**
     * @return null
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param null $name
     *
     * @return null|string
     */
    public function resolve($name = null) {
        $string = parent::resolve($name, $this, $this->App);
        if (!is_string($string)) return $string;
        return rtrim($string, '/') . '/';
    }
    /**
     * @param null $App
     *
     * @return PathContainer
     */
    public function setApp($App) {
        $this->App = $App;
        return $this;
    }


}

/**
 * Class App
 *
 * @property PathContainer $Paths
 * @property string        $name
 */
class App extends IoC {
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $this->register('Paths', PathContainer::init()->setApp($this));
    }
    public function cloneRegistry() {
        $registry = parent::cloneRegistry();
        foreach ($registry as $index => $item) {
            if ($item instanceof NativeResolvable && $item->resolve() instanceof Module) {
                $item->resolve()->reset();
            }
        }
        return $registry;
    }
    public function duplicate() {
        $Duplicate = parent::duplicate();
        $Duplicate->register('Paths', $this->resolve('Paths')->duplicate()->setApp($Duplicate));
        return $Duplicate;
    }
    public static function init() {
        return new static;
    }
    public function register($identifier, $registrand = null) {
        if (is_array($identifier)) {
            return parent::register($identifier);
        } else if (!property_exists($this, $identifier)) {
            return parent::register($identifier, $registrand);
        } else {
            $this->$identifier = $registrand;
        }
        return $this;
    }
    public function __debugInfo() {
        $return          = $this->registry;
        $return['Paths'] = $this->Paths;
        return $return;
    }
    public static function coerce($item) {
        $instance = new static();
        if ($item instanceof IoC)
            $instance->inherit($item);
        else if (is_array($item)) {
            $instance->register($item);
        }
        return $instance;
    }
    public function resolve($identifier = null, $arguments = null) {
        $arguments_class_exists = class_exists('\Sm\Abstraction\Resolvable\Arguments');
        
        if (!$arguments_class_exists || !($arguments instanceof \Sm\Abstraction\Resolvable\Arguments)) {
            $arguments = func_get_args();
            $a         = [ ];
            $one       = 0;
            foreach ($arguments as $index => $argument) {
                if (!$one++) continue;
                $a[] = $argument;
            }
            if (class_exists('\Sm\Abstraction\Resolvable\Arguments')) {
                $arguments = \Sm\Abstraction\Resolvable\Arguments::coerce($a);
            }
        }
        
        return $this->canResolve($identifier)
            ? parent::resolve($identifier, $arguments)
            : null;
    }
    
    
}