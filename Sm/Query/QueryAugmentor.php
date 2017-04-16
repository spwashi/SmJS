<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 10:52 PM
 */

namespace Sm\Query;


interface QueryAugmentor {
    /**
     * Modify a Query to fulfill a certain purpose
     *
     * @param \Sm\Query\Query $Query
     *
     * @return \Sm\Query\Query The Query that we are trying to run
     */
    public function augmentQuery(Query $Query): Query;
}