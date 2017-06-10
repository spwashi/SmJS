<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:57 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


class SourcesArrayFragment extends SqlFragment {
    /** @var  array $SourceFragmentArray An array, indexed by php alias, that keeps track of what we're calling the tables in here. */
    protected $SourceFragmentArray;
    /**
     * @return array
     */
    public function getSourceFragmentArray(): array {
        return $this->SourceFragmentArray;
    }
    /**
     * @param array $SourceFragmentArray
     *
     * @return $this
     */
    public function setSourceFragmentArray(array $SourceFragmentArray) {
        $this->SourceFragmentArray = $SourceFragmentArray;
        return $this;
    }
}