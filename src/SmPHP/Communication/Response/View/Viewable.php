<?php
/**
 * User: Sam Washington
 * Date: 2/8/17
 * Time: 11:21 PM
 */

namespace Sm\Communication\Response\View;


interface Viewable {
    public function toView(): View;
}