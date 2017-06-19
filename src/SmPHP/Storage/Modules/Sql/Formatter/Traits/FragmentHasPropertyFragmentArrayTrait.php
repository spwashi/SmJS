<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:11 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Core\Error\WrongArgumentException;
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
     * @return static|\Sm\Storage\Modules\Sql\Formatter\SqlFragment|\Sm\Core\Formatter\Fragment\Fragment
     * @throws \Sm\Core\Error\WrongArgumentException
     */
    public function setPropertyFragmentArray($PropertyFragmentArray) {
        foreach ($PropertyFragmentArray as $property_fragment) {
            if (!($property_fragment instanceof PropertyFragment)) {
                throw new WrongArgumentException("Can only set PropertyFragments to be used in SelectFragments");
            }
        }
        $this->PropertyFragmentArray = $PropertyFragmentArray;
        return $this;
    }
}