<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 1:25 AM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Core\Exception\InvalidArgumentException;

class InsertFragment extends SqlFragment {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] */
    protected $PropertyFragments = [];
    /** @var  \Sm\Core\Formatting\Fragment\Fragment[] */
    protected $ValueFragments = [];
    /** @var  \Sm\Data\Source\DataSource[] */
    protected $Sources;
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[]
     */
    public function getPropertyFragments() {
        return $this->PropertyFragments;
    }
    /**
     * Set the PropertyFragments that this InsertStatement will use. Their values are the columns.
     *
     * @param $PropertyFragments
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function setPropertyFragments($PropertyFragments) {
        foreach ($PropertyFragments as $property_fragment) {
            if (!($property_fragment instanceof PropertyFragment)) {
                throw new InvalidArgumentException("Can only set PropertyFragments to be used in InsertFragments");
            }
        }
        $this->PropertyFragments = $PropertyFragments;
        return $this;
    }
    /**
     * @param \Sm\Core\Formatting\Fragment\Fragment[] $ValueFragments
     *
     * @return InsertFragment
     */
    public function setValueFragments(array $ValueFragments) {
        $this->ValueFragments = $ValueFragments;
        return $this;
    }
    /**
     * @return \Sm\Core\Formatting\Fragment\Fragment[]
     */
    public function getValueFragmentArrays(): array {
        return $this->ValueFragments;
    }
}