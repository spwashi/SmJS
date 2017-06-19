<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:27 AM
 */

namespace Sm\Core\Application;


use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Router;
use Sm\Core\Application\Module\StandardModule;
use Sm\Core\Container\Container;
use Sm\Core\Factory\FactoryContainer;
use Sm\Core\Resolvable\FunctionResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Process\Query\Query;

/**
 * Class Application
 *
 * @property PathContainer    $Paths
 * @property ModuleContainer  $Modules
 * @property Query            $Query
 * @property string           $name
 * @property Request          $Request
 * @property Router           $Router
 * @property string           $controller_namespace
 * @property FactoryContainer $Factories
 */
class App extends Container {
    protected $app_resolved = [];
    protected $Paths;
    protected $Factories;
    protected $Modules;
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    public function __construct() {
        $PathContainer = PathContainer::init()->setApp($this);
        $this->Paths   = $PathContainer;
        parent::__construct();
        $this->Modules   = ModuleContainer::init()->setApp($this);
        $this->Factories = FactoryContainer::init();
        $this->Paths->registerDefaults(
            [
                'base_path'   =>
                    SRC_PATH,
                'app_path'    =>
                    FunctionResolvable::coerce(function ($Paths, $App) {
                        return $Paths->base_path . ($App->name??'Sm');
                    }),
                'template'    =>
                    FunctionResolvable::coerce(function ($Paths) {
                        return $Paths->app_path . 'templates/';
                    }),
                'config_path' =>
                    FunctionResolvable::coerce(function ($Paths, $App) {
                        $path = $Paths->app_path . 'config/';
                        return $path;
                    }),
            ]);
    }
    public function __get($name) {
        if (in_array($name, [ 'Paths', 'Modules', 'Factories' ])) {
            return $this->$name;
        }
        return parent::__get($name);
    }
    /**
     * Set the name of the Application
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name) {
        $this->__set('name', $name);
        return $this;
    }
    public function cloneRegistry() {
        $registry = parent::cloneRegistry();
        foreach ($registry as $index => $item) {
            if ($item instanceof NativeResolvable && $item->resolve() instanceof StandardModule) {
                $item->resolve()->reset();
            }
        }
        return $registry;
    }
    public function duplicate() {
        /** @var App $Duplicate */
        $Duplicate            = parent::duplicate();
        $Duplicate->Paths     = $this->Paths->duplicate()->setApp($Duplicate);
        $Duplicate->Modules   = $this->Modules->duplicate($Duplicate);
        $Duplicate->Factories = $this->Factories->duplicate();
        return $Duplicate;
    }
    public function register($name = null, $registrand = null, $register_with_app = false) {
        if ($register_with_app) {
            $this->app_resolved[] = $name;
        }
        if ($registrand instanceof NativeResolvable) {
            $registrand = $registrand->resolve();
        }
        return parent::register($name, $registrand);
    }
    public function registerDefaults($name, $registrand = null, $register_with_app = false) {
        if ($register_with_app) {
            if (is_array($name)) {
                foreach ($name as $index => $value) {
                    $this->app_resolved[ $index ] = true;
                }
            } else {
                $this->app_resolved[ $name ] = true;
            }
        }
        return parent::registerDefaults($name, $registrand);
    }
    /**
     * @param null  $identifier
     * @param mixed $arguments
     *
     * @return static|mixed|StandardModule
     */
    public function resolve($identifier = null, $arguments = null) {
        $arguments = func_get_args();
        #If
        if (array_key_exists($identifier, $this->app_resolved)) {
            array_splice($arguments, 1, 0, [ $this ]);
        }
        return $this->canResolve($identifier)
            ? parent::resolve(...$arguments)
            : null;
    }
    public function __debugInfo() {
        $return          = $this->getAll();
        $return['Paths'] = $this->Paths;
        return $return;
    }
    /**
     * @return \Sm\Core\Application\App|static
     */
    public static function init() {
        return new static;
    }
    public static function coerce($item) {
        $instance = new static();
    
        if ($item instanceof Container) {
            $instance->inherit($item);
        } else if (is_array($item)) {
            $instance->register($item);
        }
        
        return $instance;
    }
}