<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:43 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


class FromFragment extends SqlFragment {
    /** @var  array $SourceFragmentArray An array, indexed by php alias, that keeps track of what we're calling the tables in here. */
    protected $SourceFragmentArray;
    public function getVariables(): array {
        return [ 'aliases' => $this->SourceFragmentArray ];
    }
    /**
     * @return array
     */
    public function getSourceFragmentArray(): array {
        return $this->SourceFragmentArray;
    }
    /**
     * @param array $SourceFragmentArray
     *
     * @return FromFragment
     */
    public function setSourceFragmentArray(array $SourceFragmentArray): FromFragment {
        $this->SourceFragmentArray = $SourceFragmentArray;
        return $this;
    }
}