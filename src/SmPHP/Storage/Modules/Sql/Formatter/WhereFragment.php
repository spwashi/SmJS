<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:43 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Process\Query\WhereClause;

class WhereFragment extends SqlFragment {
    /** @var  WhereClause $where The WHERE clause */
    protected $where;
    /** @var \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] The PropertyFragments that we might reference in this Where clause */
    protected $PropertyFragments = [];
    public function getWhere() {
        return $this->where;
    }
    /**
     * @param array $clauses
     *
     * @return WhereFragment
     */
    public function setWhere(WhereClause $clauses): WhereFragment {
        $this->where = $clauses;
        return $this;
    }
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[]
     */
    public function getPropertyFragments(): array {
        return $this->PropertyFragments;
    }
    /**
     * @param \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] $PropertyFragments
     *
     * @return WhereFragment
     */
    public function setPropertyFragments(array $PropertyFragments): WhereFragment {
        $this->PropertyFragments = $PropertyFragments;
        return $this;
    }
}