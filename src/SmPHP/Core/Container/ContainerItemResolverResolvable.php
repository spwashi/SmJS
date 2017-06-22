<?php
/**
 * User: Sam Washington
 * Date: 4/16/17
 * Time: 2:56 PM
 */

namespace Sm\Core\Container;


use Sm\Core\Resolvable\OnceRunThenNullResolvable;


/**
 * Class ContainerItemResolverResolvable
 *
 * Used in Containers to return an item only once, then return Null afterwards.
 *
 * @package Sm\Core\Container
 */
class ContainerItemResolverResolvable extends OnceRunThenNullResolvable {
}