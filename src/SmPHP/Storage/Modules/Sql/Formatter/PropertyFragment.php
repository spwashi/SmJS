<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:04 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;

use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasPropertyFragmentTrait;
use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasSourceFragmentTrait;


/**
 * Class PropertyFragment
 *
 * @package Sm\Storage\Modules\Sql\Formatter
 */
class PropertyFragment extends SqlFragment {
    use FragmentHasSourceFragmentTrait;
    use FragmentHasPropertyFragmentTrait;
}