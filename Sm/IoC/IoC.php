<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:09 PM
 */

namespace Sm\IoC;


use Sm\Abstraction\Factory\Factory;
use Sm\Abstraction\Registry;
use Sm\Abstraction\Resolvable\Arguments;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;

/**
 * Class IoC
 *
 * @package Sm\IoC
 * @coupled \Sm\Abstraction\Resolvable\Resolvable
 */
class IoC implements Registry {
    protected $registry = [ ];
    /**
     * @var \Sm\Abstraction\Factory\Factory
     */
    protected $ResolvableFactory    = null;
    protected $_registered_defaults = [ ];
    
    public function __construct() {
        $this->ResolvableFactory = new ResolvableFactory;
    }
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    
    public function setResolvableFactory(Factory $ResolvableFactory) {
        $this->ResolvableFactory = $ResolvableFactory;
        return $this;
    }
    /**
     * Return a new instance of this class that inherits this registry
     *
     * @return static
     */
    public function duplicate() {
        $IoC                       = static::init();
        $registry                  = $this->cloneRegistry();
        $IoC->_registered_defaults = $this->_registered_defaults;
        $IoC->register($registry);
        return $IoC;
    }
    public function inherit(IoC $registry) {
        $this->register($registry->cloneRegistry());
    }
    public function register_defaults($name, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register_defaults($index, $item);
            }
        } else if (!$this->canResolve($name) && !array_key_exists($name, $this->_registered_defaults)) {
            $this->register($name, $registrand);
            $this->_registered_defaults[] = $name;
        }
        return $this;
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function __set($name, $value) {
        $this->register($name, $value);
    }
    
    #  Public Methods
    #-----------------------------------------------------------------------------------
    /**
     * Register an item or an array of items (indexed by name) as being things that are going to get resolved by this IoC container
     *
     * @param string|array                   $name       Could also be an associative array of whatever we are registering
     * @param Resolvable|callable|mixed|null $registrand Whatever is being registered. Null if we are registering an array
     *
     * @return $this
     */
    public function register($name = null, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->addToRegistry($index, $this->standardizeRegistrand($item, $index));
            }
        } else {
            $this->addToRegistry($name, $this->standardizeRegistrand($registrand, $name));
        }
        return $this;
    }
    /**
     * @param string $name          The name of whatever we are going to resolve
     * @param mixed  $arguments,... The arguments to whatever is being resolved (passed in as arguments after the first)
     *
     * @return mixed|null
     */
    public function resolve($name = null, $arguments = null) {
        $args = func_get_args();
        array_shift($args);
        $arguments = $arguments instanceof Arguments ? $arguments : new Arguments($args);
        $item      = $this->getItem($name);
        
        if (!($item instanceof Resolvable)) return $item;
        
        return $item->resolve($arguments);
    }
    public function canResolve($name) {
        return null !== ($this->getItem($name));
    }
    public static function init() { return new static; }
    protected function cloneRegistry() {
        $registry     = $this->registry;
        $new_registry = [ ];
        foreach ($registry as $identifier => $item) {
            $new_registry[ $identifier ] = $item instanceof \Sm\Resolvable\Resolvable ? $item->reset() : $item;
        }
        return $new_registry;
    }
    
    #  Private/Protected methods
    #-----------------------------------------------------------------------------------
    /**
     * @param mixed $registrand   Whatever is being registered
     * @param null  $name         The name of whatever is being registered.
     *                            This is the second argument because we don't necessarily have to provide it. It can be left off.
     *
     * @return null|\Sm\Abstraction\Resolvable\Resolvable
     */
    protected function standardizeRegistrand($registrand, $name = null) {
        return isset($this->ResolvableFactory) ? $this->ResolvableFactory->build($registrand) : null;
    }
    /**
     * @param string                                $name
     * @param \Sm\Abstraction\Resolvable\Resolvable $item
     *
     * @return $this
     */
    protected function addToRegistry($name, $item) {
        $this->registry[ $name ] = $item;
        if (($index = array_search($name, $this->_registered_defaults)) > -1) {
            unset($this->_registered_defaults[ $index ]);
        }
        return $this;
    }
    /**
     * @param $name
     *
     * @return \Sm\Abstraction\Resolvable\Resolvable|null
     */
    protected function getItem($name) {
        if (!is_string($name)) return null;
        return $this->registry[ $name ] ?? null;
    }
}