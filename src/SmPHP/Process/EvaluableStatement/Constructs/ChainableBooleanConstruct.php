<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 2:55 PM
 */

namespace Sm\Process\EvaluableStatement\Constructs;

/**
 * Trait ChainableBooleanConstruct
 *
 * @property-read array  $items
 * @property-read string $construct
 * @method  array items(bool $do_format = true)
 * @method  array construct(bool $do_format = true)
 *
 * @package Sm\Process\EvaluableStatement\Constructs
 */
trait ChainableBooleanConstruct {
    use ChainableConstructTrait;
}