<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 1:25 AM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Error\WrongArgumentException;

class InsertFragment extends SqlFragment {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] */
    protected $PropertyFragments = [];
    /** @var  \Sm\Formatter\Fragment\Fragment[] */
    protected $ValueFragments = [];
    /** @var  \Sm\Storage\Source\Source[] */
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
     * @throws \Sm\Error\WrongArgumentException
     */
    public function setPropertyFragments($PropertyFragments) {
        foreach ($PropertyFragments as $property_fragment) {
            if (!($property_fragment instanceof PropertyFragment)) {
                throw new WrongArgumentException("Can only set PropertyFragments to be used in InsertFragments");
            }
        }
        $this->PropertyFragments = $PropertyFragments;
        return $this;
    }
    /**
     * Return the variables that this class deems relevant to the formatter.
     *
     * @return array
     */
    public function getVariables(): array {
        return [
            'Properties' => $this->PropertyFragments,
        ];
    }
    /**
     * @param \Sm\Formatter\Fragment\Fragment[] $ValueFragments
     *
     * @return InsertFragment
     */
    public function setValueFragments(array $ValueFragments) {
        $this->ValueFragments = $ValueFragments;
        return $this;
    }
    /**
     * @return \Sm\Formatter\Fragment\Fragment[]
     */
    public function getValueFragmentArrays(): array {
        return $this->ValueFragments;
    }
}