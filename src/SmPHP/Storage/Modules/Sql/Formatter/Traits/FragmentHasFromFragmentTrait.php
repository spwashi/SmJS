<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:03 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Formatter\Fragment\Fragment;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;

trait FragmentHasFromFragmentTrait {
    /**
     * @var FromFragment $FromFragment
     */
    protected $FromFragment;
    /**
     * @return FromFragment
     */
    public function getFromFragment(): FromFragment {
        return $this->FromFragment;
    }
    /**
     * @param FromFragment $FromFragment
     *
     * @return Fragment|static
     */
    public function setFromFragment(FromFragment $FromFragment): Fragment {
        $this->FromFragment = $FromFragment;
        return $this;
    }
}