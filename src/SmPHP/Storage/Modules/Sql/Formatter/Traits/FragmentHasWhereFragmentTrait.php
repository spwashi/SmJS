<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:09 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Core\Formatter\Fragment\Fragment;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;

trait FragmentHasWhereFragmentTrait {
    /** @var  WhereFragment $WhereFragment */
    protected $WhereFragment;
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
    public function setWhereFragment(WhereFragment $WhereFragment): Fragment {
        $this->WhereFragment = $WhereFragment;
        return $this;
    }
}