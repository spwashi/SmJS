<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 4:47 PM
 */

namespace Sm\Abstraction\Formatting;


use Sm\Abstraction\Resolvable\Resolvable;

interface Formatter extends Resolvable {
    public function resolve(array $variables = []);
}