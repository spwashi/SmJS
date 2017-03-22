<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:09 PM
 *
 * @package Sm
 *
 */

namespace Sm\Container;


use Sm\Abstraction\Factory\Factory;
use Sm\Abstraction\Iterator\IteratorTrait;
use Sm\Abstraction\Registry;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;

/**
 * Class Container
 *
 * @package Sm\Container
 *
 * @method Resolvable current()
 *
 *
 * @coupled \Sm\Abstraction\Resolvable\Resolvable
 */
class Container implements Registry, \Iterator {
    use IteratorTrait;
    
    protected $registry = [];
    
    /**
     * @var \Sm\Abstraction\Factory\Factory
     */
    protected $ResolvableFactory    = null;
    protected $_registered_defaults = [];
    
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
     * @return static|$this
     */
    public function duplicate() {
        $Container                       = static::init();
        $registry                        = $this->cloneRegistry();
        $Container->_registered_defaults = $this->_registered_defaults;
    
    
        $Container->register($registry);
        return $Container;
    }
    public function inherit(Container $registry) {
        $this->register($registry->cloneRegistry());
    }
    public function register_defaults($name, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $item) {
                $this->register_defaults($index, $item);
            }
            return $this;
        }
    
        if (($this->_registered_defaults[ $name ] ?? false) || !($this->canResolve($name))) {
            $this->register($name, $registrand);
            $this->_registered_defaults[ $name ] = true;
        }
        return $this;
    }
    public function __get($name) {
        return $this->resolve($name);
    }
    public function __set($name, $value) {
        $this->register($name, $value);
    }
    
    #  Iterator methods
    #-----------------------------------------------------------------------------------
    /**
     * Get the Key that we are going to iterate on
     *
     * @return null|string
     */
    public function getRegistryName() {
        return isset($this->registry) ? 'registry' : null;
    }
    
    #  Public Methods
    #-----------------------------------------------------------------------------------
    /**
     * Register an item or an array of items (indexed by name) as being things that are going to get resolved by this Container container
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
     * @param string $name The name of whatever we are going to resolve
     *
     * @return mixed|null
     */
    public function resolve($name = null) {
        $args = func_get_args();
        array_shift($args);
        $item = $this->getItem($name);
        
        if (!($item instanceof Resolvable)) return $item;
        
        return $item->resolve(...$args);
    }
    /**
     * Can we resolve what we're trying to?
     *
     * @param $name
     *
     * @return bool
     */
    public function canResolve($name) {
        return null !== ($this->getItem($name));
    }
    #  Private/Protected methods
    #-----------------------------------------------------------------------------------
    /**
     * @return \Sm\Container\Container|static
     */
    public static function init() { return new static; }
    /**
     * Duplicate the registry for the sake of inheritance
     *
     * @return array
     */
    protected function cloneRegistry() {
        $registry     = $this->registry;
        $new_registry = [];
        foreach ($registry as $identifier => $item) {
            if ($identifier) {
                if (is_object($item)) {
                    $new_registry[ $identifier ] = clone $item;
                } else {
                    $new_registry[ $identifier ] = $item;
                }
            }
        }
        return $new_registry;
    }
    /**
     * @param mixed $registrand Whatever is being registered
     *
     * @return null|\Sm\Abstraction\Resolvable\Resolvable
     */
    protected function standardizeRegistrand($registrand) {
        return isset($this->ResolvableFactory) ? $this->ResolvableFactory->build($registrand) : null;
    }
    /**
     * Add something to the registry (meant to represent the actual action)
     *
     * @param string                                $name
     * @param \Sm\Abstraction\Resolvable\Resolvable $item
     *
     * @return $this
     */
    protected function addToRegistry($name, $item) {
        $this->registry[ $name ] = $item;
        if ($this->_registered_defaults[ $name ]??false) {
            unset($this->_registered_defaults[ $name ]);
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