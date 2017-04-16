<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Storage\Modules\Sql\Formatter\SourcesArrayFragment;
use Sm\Storage\Modules\Sql\Formatter\UpdateFragment;

/**
 * Class UpdateStatementSubInterpreter
 *
 * Meant to handle the execution of Update statements
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
class UpdateStatementSubInterpreter extends MysqlQuerySubInterpreter {
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        # The tables we're updating
        $SourcesArrayFragment = SourcesArrayFragment::init()->setSourceFragmentArray($this->createSourceFragments());
        # The columns we're deleting
        $PropertyFragments = $this->createPropertyFragments();
        # The "Where" clause that we're going to add on (if it exists)
        $WhereFragment = $this->createWhereFragment($PropertyFragments);
        
        $Fragment = UpdateFragment::init();
        $Fragment->setSourcesArrayFragment($SourcesArrayFragment);
        $Fragment->setWhereFragment($WhereFragment);
        $Fragment->setPropertyFragments($PropertyFragments);
        
        return $Fragment;
    }
}