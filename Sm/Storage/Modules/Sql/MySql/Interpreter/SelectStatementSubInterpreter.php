<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Abstraction\Identifier\Identifier;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;
use Sm\Storage\Modules\Sql\Formatter\SelectFragment;
use Sm\Util;

/**
 * Class SelectStatementSubInterpreter
 *
 * Meant to handle the execution of Select statements
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
class SelectStatementSubInterpreter extends MysqlQuerySubInterpreter {
    public function execute() {
        return parent::execute();
        
        /** @var \Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource $DatabaseSource */
        $DatabaseSource = $this->SqlModule->getDatabaseSource();
        # todo Variables being bound
        
        $sth = $DatabaseSource->getConnection()->prepare("$SelectStatement");
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getQueryProperties() {
        return $this->Query->getSelectArray();
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
        $PropertyFragments = $this->createPropertyFragments();
        # The "Where" clause that we're going to add on (if it exists)
        $WhereFragment = $this->createWhereFragment($PropertyFragments);
        
        $Fragment = SelectFragment::init()
                                  ->setFrom($FromFragment)
                                  ->setPropertyFragments($PropertyFragments)
                                  ->setWhereFragment($WhereFragment);
        
        return $Fragment;
    }
    protected function createFromFragment() {
        $SourceFragments = $this->createSourceFragments();
        return FromFragment::init()->setSourceFragmentArray($SourceFragments);
    }
    /**
     * Initialize an array that maps the Object ID of a Source to an array of numbers indexed by the object ID of an Owner
     *
     * @param bool $redo
     *
     * @return $this
     */
    protected function initSourceMap($redo = false) {
        parent::initSourceMap($redo);
        $src_ownr_map = $this->Source_object_id__Owner_object_id_array__map;
        foreach ($src_ownr_map as $source_id => $OwnerObjectContainer) {
            foreach ($OwnerObjectContainer as $object_id => $count) {
                # If there are multiple owners that use this source, alias it
                if (!$count) continue;
                
                $alias_key = Identifier::combineObjectIds($source_id, $object_id);
                
                # If we've already aliased this Source, don't do it again
                if ($this->SqlModule->FormatterFactory->Aliases->canResolve($alias_key)) continue;
                
                #
                $_alias = Util::generateRandomString(5, Util::getAlphaCharacters(0));
                $this->SqlModule->FormatterFactory->Aliases->register($alias_key, $_alias);
            }
        }
        return $this;
        
    }
}