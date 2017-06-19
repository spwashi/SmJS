<?php
/**
 * User: Sam Washington
 * Date: 4/6/17
 * Time: 10:34 AM
 */

namespace Sm\Core\Container;

use Sm\Core\Container\Cache\CacheItem;
use Sm\Core\Util;

/**
 * Class ContainerHasMiniCacheTrait
 *
 * For containers whose values we want to cache
 *
 * #todo todo test factory, make it a container, implement caching
 *
 * @package Sm\Core\Container
 */
trait ContainerHasMiniCacheTrait {
    
    #region Cache functions
    /** @var \Sm\Core\Container\Cache\CacheItem[][] An array of the things that we've already resolved, indexed by type */
    protected $ResolveCacheItems           = [];
    protected $registry_count              = 0;
    protected $_cache_has_been_invalidated = false;
    protected $resolve_cache_key;
    
    /**
     * @return $this
     */
    public function invalidateCache() {
        $this->_cache_has_been_invalidated = true;
        return $this;
    }
    /**
     * Start a cache of the things that we might resolve.
     *
     * @param string|null $key If supplied, only things that
     *
     * @return $this
     * @throws \Sm\Core\Error\Error
     */
    public function startCache($key = '-') {
        if (isset($this->resolve_cache_key) && ($this->resolve_cache_key !== '-') && $key !== $this->resolve_cache_key) {
            # If the cache key doesn't match what was already set, we're trying to do something we don't intend on doing
            return $this;
        }
        $this->resolve_cache_key = $key;
        return $this;
    }
    /**
     * Stop caching things
     *
     * @param string $key
     *
     * @return $this
     */
    public function endCache($key = '-') {
        if ($key !== $this->resolve_cache_key) {
            return $this;
        }
        $this->resolve_cache_key = null;
        $this->clearCache($key);
        return $this;
    }
    /**
     * Empty out the cache
     *
     * @param string $key The "key" that we are using to control access to the cache.
     *
     * @return $this
     */
    protected function clearCache($key = '-') {
        if ($key !== $this->resolve_cache_key) {
            return $this;
        }
        $this->ResolveCacheItems = [];
        return $this;
    }
    /**
     * Retrieve an item from the cache based on whatever was used to put it there
     *
     * @param $arguments
     *
     * @return mixed|null
     */
    /**
     * Given the result of a query with a set of arguments
     *
     * @param mixed $result    The Result that we want to cache
     * @param array $arguments The arguments that were passed into "resolve" that were used to get there
     *
     * @return $this
     */
    protected function cacheResult($result, array $arguments) {
        if (!isset($this->resolve_cache_key)) {
            return $this;
        }
        if (!isset($result)) {
            return $this;
        }
        
        $cache_key = $this->generateCacheIndex($arguments);
        # Index the Resolve cache by the perceived handle of the function called by them.
        # Use the types of arguments to make it easier to determine if something is in a cache
        $this->ResolveCacheItems[ $cache_key ] = $this->ResolveCacheItems[ $cache_key ] ??[];
        
        # Add the CacheItem to the array of CacheItems
        $this->ResolveCacheItems[ $cache_key ][] = CacheItem::init($result)->setIdentity($arguments);
        
        return $this;
    }
    /**
     * Generate the Index at which the result of Container::resolve will be held for a set of arguments
     *
     * @param $result
     *
     * @return string
     */
    private function generateCacheIndex($result) {
        return Util::getShapeOfItem($result);
    }
    #endregion
    
}