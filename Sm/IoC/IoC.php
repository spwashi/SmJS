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
    protected $ResolvableFactory = null;
    
    
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    
    public function __construct(Factory $ResolvableFactory = null) { $this->ResolvableFactory = $ResolvableFactory; }
    /**
     * Static Constructor for the IoC class
     *
     * @param Factory|null $ResolvableFactory
     *
     * @return static
     */
    public static function init(Factory $ResolvableFactory = null) { return new static($ResolvableFactory); }
    
    /**
     * Return a new instance of this class that inherits this registry
     *
     * @return IoC
     */
    public function duplicate() {
        $IoC      = static::init($this->ResolvableFactory);
        $registry = $this->cloneRegistry();
        $IoC->register($registry);
        return $IoC;
    }
    
    protected function cloneRegistry() {
        $registry     = $this->registry;
        $new_registry = [ ];
        foreach ($registry as $identifier => $item) {
            $new_registry[ $identifier ] = $item instanceof \Sm\Resolvable\Resolvable ? $item->reset() : $item;
        }
        return $new_registry;
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
    public function register($name, $registrand = null) {
        if (is_array($name)) {
            foreach ($name as $index => $registrand) {
                $this->addToRegistry($index, $this->standardizeRegistrand($registrand, $index));
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
    public function resolve($name, $arguments = null) {
        $arguments = $arguments instanceof Arguments ? $arguments : new Arguments(func_get_args());
        $item      = $this->getItem($name);
        
        if (!($item instanceof Resolvable)) return $item;
        
        return $item->resolve($arguments);
    }
    
    public function canResolve($name) {
        return null !== ($this->getItem($name));
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