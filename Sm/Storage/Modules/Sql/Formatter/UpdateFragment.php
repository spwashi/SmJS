<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 7:01 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Formatter\Fragment\Fragment;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasPropertyFragments;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasWhereFragmentTrait;

class UpdateFragment extends SqlFragment {
    use FragmentHasPropertyFragments;
    use FragmentHasWhereFragmentTrait;
    
    /**
     * @var SourcesArrayFragment $SourcesArrayFragment
     */
    protected $SourcesArrayFragment;
    /**
     * @return SourcesArrayFragment
     */
    public function getSourcesArrayFragment(): SourcesArrayFragment {
        return $this->SourcesArrayFragment;
    }
    /**
     * @param SourcesArrayFragment $SourcesArrayFragment
     *
     * @return Fragment|static
     */
    public function setSourcesArrayFragment(SourcesArrayFragment $SourcesArrayFragment): Fragment {
        $this->SourcesArrayFragment = $SourcesArrayFragment;
        return $this;
    }
}