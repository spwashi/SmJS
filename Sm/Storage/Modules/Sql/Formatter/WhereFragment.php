<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:43 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Query\Where;

class WhereFragment extends SqlFragment {
    /** @var  Where $where The WHERE clause */
    protected $where;
    public function getVariables(): array {
        return [ 'clauses' => $this->where ];
    }
    public function getWhere() {
        return $this->where;
    }
    /**
     * @param array $clauses
     *
     * @return WhereFragment
     */
    public function setWhere(Where $clauses): WhereFragment {
        $this->where = $clauses;
        return $this;
    }
}