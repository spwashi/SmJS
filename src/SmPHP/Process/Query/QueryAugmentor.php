<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 10:52 PM
 */

namespace Sm\Process\Query;


interface QueryAugmentor {
    /**
     * Modify a Query to fulfill a certain purpose
     *
     * @param \Sm\Process\Query\Query $Query
     *
     * @return \Sm\Process\Query\Query The Query that we are trying to run
     */
    public function augmentQuery(Query $Query): Query;
}