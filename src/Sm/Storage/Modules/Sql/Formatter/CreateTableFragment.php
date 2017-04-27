<?php
/**
 * User: Sam Washington
 * Date: 4/16/17
 * Time: 9:19 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasSourceFragmentTrait;

class CreateTableFragment extends SqlFragment {
    use FragmentHasSourceFragmentTrait;
    protected $ColumnFragmentArray = [];
    /**
     * @return array
     */
    public function getColumnFragmentArray(): array {
        return $this->ColumnFragmentArray;
    }
    /**
     * @param array $ColumnFragmentArray
     *
     * @return $this
     */
    public function setColumnFragmentArray(array $ColumnFragmentArray) {
        $this->ColumnFragmentArray = $ColumnFragmentArray;
        return $this;
    }
    
}