<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Storage\Modules\Sql\Formatter\DeleteFragment;

/**
 * Class DeleteStatementSubInterpreter
 *
 * Meant to handle the execution of Delete statements
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
class DeleteStatementSubInterpreter extends MysqlQuerySubInterpreter {
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        # The "From" Clause
        $FromFragment = $this->createFromFragment();
        # The columns we're deleting
        $PropertyFragments = $this->createPropertyFragments();
        # The "Where" clause that we're going to add on (if it exists)
        $WhereFragment = $this->createWhereFragment($PropertyFragments);
        
        $Fragment = DeleteFragment::init();
        $Fragment->setFromFragment($FromFragment);
        $Fragment->setWhereFragment($WhereFragment);
        $Fragment->setPropertyFragments($PropertyFragments);
        
        return $Fragment;
    }
}