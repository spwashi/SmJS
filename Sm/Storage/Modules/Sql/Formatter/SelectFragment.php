<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:17 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Error\WrongArgumentException;

class SelectFragment extends SqlFragment {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] */
    protected $PropertyFragments;
    /**
     * @var FromFragment $FromFragment
     */
    protected $FromFragment;
    protected $WhereFragment;
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
     * @return $this
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
    public function getVariables(): array {
        return [
            'Properties' => $this->PropertyFragments,
            'From'       => $this->FromFragment,
        ];
    }
    /**
     * @return FromFragment
     */
    public function getFrom(): FromFragment {
        return $this->FromFragment;
    }
    /**
     * @param FromFragment $FromFragment
     *
     * @return SelectFragment
     */
    public function setFrom(FromFragment $FromFragment): SelectFragment {
        $this->FromFragment = $FromFragment;
        return $this;
    }
    /**
     * @return WhereFragment
     */
    public function getWhereFragment() {
        return $this->WhereFragment;
    }
    /**
     * @param \Sm\Storage\Modules\Sql\Formatter\WhereFragment $WhereFragment
     *
     * @return \Sm\Storage\Modules\Sql\Formatter\SelectFragment
     */
    public function setWhereFragment(WhereFragment $WhereFragment): SelectFragment {
        $this->WhereFragment = $WhereFragment;
        return $this;
    }
}