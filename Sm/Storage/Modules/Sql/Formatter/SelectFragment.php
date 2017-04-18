<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:17 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasFromFragmentTrait;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasPropertyFragmentArrayTrait;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasWhereFragmentTrait;

class SelectFragment extends SqlFragment {
    use FragmentHasFromFragmentTrait;
    use FragmentHasWhereFragmentTrait;
    use FragmentHasPropertyFragmentArrayTrait;
}