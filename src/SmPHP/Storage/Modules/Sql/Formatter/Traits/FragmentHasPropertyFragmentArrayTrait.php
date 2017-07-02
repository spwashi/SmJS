<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:11 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;

trait FragmentHasPropertyFragmentArrayTrait {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] */
    protected $PropertyFragmentArray;
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[]
     */
    public function getPropertyFragmentArray() {
        return $this->PropertyFragmentArray;
    }
    /**
     * Set the PropertyFragments that this SelectStatement will use.
     *
     * @param $PropertyFragmentArray
     *
     * @return static|\Sm\Storage\Modules\Sql\Formatter\SqlFragment|\Sm\Core\Formatting\Fragment\Fragment
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function setPropertyFragmentArray($PropertyFragmentArray) {
        foreach ($PropertyFragmentArray as $property_fragment) {
            if (!($property_fragment instanceof PropertyFragment)) {
                throw new InvalidArgumentException("Can only set PropertyFragments to be used in SelectFragments");
            }
        }
        $this->PropertyFragmentArray = $PropertyFragmentArray;
        return $this;
    }
}