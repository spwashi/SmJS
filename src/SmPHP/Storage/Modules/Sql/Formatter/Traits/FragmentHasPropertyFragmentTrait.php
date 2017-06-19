<?php
/**
 * User: Sam Washington
 * Date: 4/17/17
 * Time: 12:15 AM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;

trait FragmentHasPropertyFragmentTrait {
    /** @var  \Sm\Data\Property\Property $Property The Property that this Fragment represents */
    protected $Property;
    /**
     * @return \Sm\Data\Property\Property
     */
    public function getProperty() {
        return $this->Property;
    }
    /**
     * @param mixed $Property
     *
     * @return PropertyFragment
     */
    public function setProperty($Property) {
        $this->Property = $Property;
        return $this;
    }
}