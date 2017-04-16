<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:11 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Error\WrongArgumentException;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;

trait FragmentHasPropertyFragments {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] */
    protected $PropertyFragments;
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[]
     */
    public function getPropertyFragments() {
        return $this->PropertyFragments;
    }
    /**
     * Set the PropertyFragments that this SelectStatement will use.
     *
     * @param $PropertyFragments
     *
     * @return static|\Sm\Storage\Modules\Sql\Formatter\SqlFragment|\Sm\Formatter\Fragment\Fragment
     * @throws \Sm\Error\WrongArgumentException
     */
    public function setPropertyFragments($PropertyFragments) {
        foreach ($PropertyFragments as $property_fragment) {
            if (!($property_fragment instanceof PropertyFragment)) {
                throw new WrongArgumentException("Can only set PropertyFragments to be used in SelectFragments");
            }
        }
        $this->PropertyFragments = $PropertyFragments;
        return $this;
    }
}