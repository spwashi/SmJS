<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:09 PM
 *
 * @package Sm
 *
 */

namespace Sm\Storage\Container;


use Sm\Abstraction\Registry;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Factory\Factory;
use Sm\Resolvable\ResolvableFactory;
use Sm\Storage\Container\Mini\MiniCache;

/**
 * Class Container
 *
 * @package Sm\Storage\Container
 *
 * @method Resolvable current()
 * @property \Sm\Storage\Container\Mini\MiniCache $Cache
 *
 * @coupled \Sm\Abstraction\Resolvable\Resolvable
 */
class Container extends AbstractContainer implements Registry, \Iterator {
    use ContainerHasMiniCacheTrait;
    
    /**
     * @var Factory
     */
    protected $ResolvableFactory = null;
    
    /** @var bool */
    protected $do_consume = false;
    /** @var  MiniCache $ConsumedItems */
    protected $ConsumedItems;
    
    
    /**
     * Container constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->ResolvableFactory = new ResolvableFactory;
    }
    /**
     * Determine how we are going to "consume" resources.
     * In other words, should we only resolve something once, and return null for everything else?
     * Should we only resolve once for one set of arguments?
     *
     * @param bool $do_consume
     *
     * @return $this
     */
    public function setConsumptionMode($do_consume = true) {
        $this->do_consume    = $do_consume;
        $this->ConsumedItems = $this->ConsumedItems ?? MiniCache::begin();
        return $this;
    }
    /**
     * Reset the list of items that we've "consumed" for a certain set of arguments
     *
     * @return $this
     */
    public function resetConsumedItems() {
        $this->ConsumedItems = MiniCache::begin();
        return $this;
    }
    public function resolve($name = null) {
        $args = func_get_args();
        array_shift($args);
        
        # If we've already resolved for this item and that matters, return
        if ($this->do_consume && $this->ConsumedItems->resolve($args)) {
            return null;
        }
        
        # Check the cache first for the result
        if (null !== ($result = $this->Cache->resolve($args))) {
            return $result;
        }
        
        # Try to resolve as usual
        $result = parent::resolve($name, ...$args);
        
        if (!isset($result)) {
            return null;
        }
        
        # If we want to keep track of what we've consumed,
        if ($this->do_consume) {
            $this->ConsumedItems->cache($args, true);
        }
        
        # Cache the result if we've decided that's necessary (the cache was started)
        $this->Cache->cache($args, $result);
        
        
        return $result;
    }
    protected function standardizeRegistrand($registrand) {
        return isset($this->ResolvableFactory) ? $this->ResolvableFactory->build($registrand) : null;
    }
}