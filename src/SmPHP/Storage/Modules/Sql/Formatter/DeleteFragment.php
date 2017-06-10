<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 6:01 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasFromFragmentTrait;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasPropertyFragmentArrayTrait;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasWhereFragmentTrait;

class DeleteFragment extends SqlFragment {
    use FragmentHasFromFragmentTrait;
    use FragmentHasWhereFragmentTrait;
    use FragmentHasPropertyFragmentArrayTrait;
}