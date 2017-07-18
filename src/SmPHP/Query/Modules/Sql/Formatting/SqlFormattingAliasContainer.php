<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 5:25 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Container\Container;
use Sm\Core\Exception\Error;

/**
 * Class SqlFormattingAliasContainer
 *
 * Container keeping track of the things wi
 *
 * @package Sm\Query\Modules\Sql\Formatting
 */
class SqlFormattingAliasContainer extends Container {
    
    /**
     * If we alias something and that gets aliased, we want to be able to get the last alias of the item.
     *
     * Keep trying to find the alias of something if we can
     *
     * @param string $item
     * @param null   $fallback Something to fall back on if the Alias doesn't actually exist.
     *
     * @return mixed|null|string
     * @throws \Sm\Core\Exception\Error
     */
    public function getFinalAlias($item, $fallback = null) {
        $aliased = $item;
        $count   = 0;
        # Loop through, replacing "next_alias" with the result of "resolve". Stop once there are no more aliases
        while ($next_alias = $this->resolve($aliased)) {
            $count++;
            $aliased = $next_alias;
            if ($count === 15) throw new Error("Looks like there might be some recursion. 15 calls to 'resolve'");
        }
        
        # If we've provided a fallback, use it
        if ($item === $aliased && isset($fallback)) {
            $this->register($fallback, $item);
        }
        
        return $aliased;
    }
}