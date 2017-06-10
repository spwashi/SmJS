<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Storage\Modules\Sql\Formatter\SelectFragment;

/**
 * Class SelectStatementSubInterpreter
 *
 * Meant to handle the execution of Select statements
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
class SelectQuerySubInterpreter extends MysqlQuerySubInterpreter {
    public function execute() {
        return parent::execute();
        
        /** @var \Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource $DatabaseSource */
        $DatabaseSource = $this->SqlModule->getDatabaseSource();
        # todo Variables being bound
        
        $sth = $DatabaseSource->getConnection()->prepare("$SelectStatement");
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        # The "From" Clause
        $FromFragment = $this->createFromFragment();
        # The columns we're selecting
        $PropertyFragments = $this->createPropertyFragmentArray();
        # The "Where" clause that we're going to add on (if it exists)
        $WhereFragment = $this->createWhereFragment($PropertyFragments);
    
        $Fragment = SelectFragment::init();
        $Fragment->setFromFragment($FromFragment);
        $Fragment->setWhereFragment($WhereFragment);
        $Fragment->setPropertyFragmentArray($PropertyFragments);
        return $Fragment;
    }
}