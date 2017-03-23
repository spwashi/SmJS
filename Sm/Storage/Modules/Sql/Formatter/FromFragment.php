<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:43 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


class FromFragment extends SqlFragment {
    /** @var  array $aliases An array, indexed by php alias, that keeps track of what we're calling the tables in here. */
    protected $aliases;
    public function getVariables(): array {
        return [ 'aliases' => $this->aliases ];
    }
    /**
     * @return array
     */
    public function getAliases(): array {
        return $this->aliases;
    }
    /**
     * @param array $aliases
     *
     * @return FromFragment
     */
    public function setAliases(array $aliases): FromFragment {
        $this->aliases = $aliases;
        return $this;
    }
}