<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 10:15 AM
 */

namespace Sm\Storage\Container\Cache;


use Sm\Resolvable\NativeResolvable;

class CacheItem extends NativeResolvable {
    /** @var  string|array|mixed $identity Something that we can use to determine if this Cache item matches another */
    protected $identity;
    /**
     * Check to see if an item matches this Cache item
     *
     * @param $item
     *
     * @return bool
     */
    public function compareIdentity($item) {
        $identity = $this->identity;
        return $item === $identity;
    }
    /**
     * @param array $identity
     *
     * @return \Sm\Storage\Container\Cache\CacheItem
     */
    public function setIdentity($identity) {
        $this->identity = $identity;
        return $this;
    }
}