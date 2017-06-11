<?php
/**
 * User: Sam Washington
 * Date: 4/16/17
 * Time: 11:21 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter\Traits;


use Sm\Storage\Modules\Sql\Formatter\SourceFragment;

trait FragmentHasSourceFragmentTrait {
    /** @var  \Sm\Storage\Modules\Sql\Formatter\SourceFragment $SourceFragment The Fragment that represents this Property's DataSource */
    protected $SourceFragment;
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\SourceFragment
     */
    public function getSourceFragment() {
        return $this->SourceFragment;
    }
    /**
     * @param \Sm\Storage\Modules\Sql\Formatter\SourceFragment $SourceFragment
     *
     * @return $this
     */
    public function setSourceFragment(SourceFragment $SourceFragment) {
        $this->SourceFragment = $SourceFragment;
        return $this;
    }
}