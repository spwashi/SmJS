<?php
/**
 * User: Sam Washington
 * Date: 2/15/17
 * Time: 11:25 AM
 */

namespace Sm\Resolvable;


use Sm\Factory\Factory;
use Sm\System_\System_;

/**
 * Class ResolvableFactoryHaver
 *
 * @package Sm\Resolvable
 * @untested
 */
trait ResolvableFactoryHaver {
    /** @var  ResolvableFactory $ResolvableFactory The factory that should be used to create other Resolvables */
    protected $ResolvableFactory;
    /**
     * Get the ResolvableFactory that this Resolvable is going to use
     *
     * @return \Sm\Resolvable\ResolvableFactory
     */
    public function getResolvableFactory(): Factory {
        /** @var ResolvableFactory $ResolvableFactory */
        $ResolvableFactory = $this->ResolvableFactory ?? System_::Factory(Resolvable::class);
        return $ResolvableFactory;
    }
    /**
     * @param ResolvableFactory $ResolvableFactory
     *
     * @return static
     */
    public function setResolvableFactory(ResolvableFactory $ResolvableFactory) {
        $this->ResolvableFactory = $ResolvableFactory;
        return $this;
    }
}