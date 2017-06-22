<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:27 AM
 */

namespace Sm\Application;


use Sm\Application\Module\StandardModule;
use Sm\Communication\Request\Request;
use Sm\Communication\Routing\Router;
use Sm\Core\Container\Container;
use Sm\Core\Context\ResolutionContext;
use Sm\Core\Error\Error;
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
    
    /** @var  \Sm\Core\Context\ResolutionContext A Context that tells us the kinds of Paths, Factories, etc we have access to */
    protected $ResolutionContext;
    protected $Modules;
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $this->ResolutionContext = new ResolutionContext();
        $this->ResolutionContext->setPathContainer(PathContainer::init())
                                ->setFactoryContainer(FactoryContainer::init());
        
        $this->Modules = ModuleContainer::init()->setApp($this);
        $App           = $this;
        $this->Paths->registerDefaults(
            [
                'base_path'   => SRC_PATH,
                'app_path'    =>
                    FunctionResolvable::init(function (PathContainer $Paths) use ($App) { return $Paths->base_path . ($App->name??'Sm'); }),
                'template'    =>
                    FunctionResolvable::init(function (PathContainer $Paths) { return $Paths->app_path . 'templates/'; }),
                'config_path' =>
                    FunctionResolvable::init(function (PathContainer $Paths) { return $Paths->app_path . 'config/'; }),
            ]);
    }
    public function setModuleContainer(ModuleContainer $moduleContainer) {
        $this->Modules = $moduleContainer;
    }
    public function __get($name) {
        if (in_array($name, [ 'Paths', 'Factories' ])) {
            /**
             * @see \Sm\Core\Context\ResolutionContext::$Paths
             * @see \Sm\Core\Context\ResolutionContext::$Factories
             */
            return $this->ResolutionContext->$name;
        }
        if ($name === 'Modules') {
            if (isset($this->Modules)) {
                return $this->Modules;
            }
            throw new Error("This app has not been configured to accept any Modules.");
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
        $Duplicate = parent::duplicate();
        $Duplicate->ResolutionContext->setPathContainer($this->Paths->duplicate());
        $Duplicate->ResolutionContext->setFactoryContainer($this->Factories->duplicate());
        $Duplicate->Modules = $this->Modules->duplicate($Duplicate);
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
     * @param Container|array $item
     *
     * @return \Sm\Application\App|static
     */
    public static function init($item = null) {
        $instance = new static;
        
        if ($item instanceof Container) {
            $instance->inherit($item);
        } else if (is_array($item)) {
            $instance->register($item);
        }
        
        return $instance;
    }
}