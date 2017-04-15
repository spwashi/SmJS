<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\Interpreter;

use Sm\Query\Query;
use Sm\Query\Where;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;
use Sm\Storage\Modules\Sql\SqlModule;

/**
 * Class SubQueryInterpreter
 *
 * A class that represents the different types of queries that can be interpreted by this Query Interpreter
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
abstract class QuerySubInterpreter {
    /** @var  SqlModule $SqlModule */
    protected $SqlModule;
    /** @var  array An array indexed by the object_id of the Property owner that contains arrays indexed by the object_id of the Owner's Properties that we are Querying to the actual Property. */
    protected $Owner_object_id__Properties_map;
    /** @var  Query $Query The Query that we are going to interpret */
    protected $Query;
    /** @var  array An array that maps the object_id of the Source to the object_id of the Owners that use it */
    protected $Source_object_id__Owner_object_id_array__map;
    
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    abstract public function createFragment();
    /**
     * Create the sub interpreter to begin the process of interpreting one type of query
     *
     * @param \Sm\Query\Query                   $Query
     *
     * @param \Sm\Storage\Modules\Sql\SqlModule $SqlModule
     *
     * @return static
     *
     */
    abstract public static function create(Query $Query, SqlModule $SqlModule);
    /**
     * Create a semi-formatted string of the "Where" clause of this Query
     *
     * @param \Sm\Storage\Modules\Sql\Formatter\PropertyFragment[] $PropertyFragments An array of PropertyFragments that might be mentioned
     *
     * @return mixed
     */
    protected function createWhereFragment($PropertyFragments = []): WhereFragment {
        $Where         = $this->Query->getWhere();
        $WhereFragment = WhereFragment::init()
                                      ->setPropertyFragments($PropertyFragments);
        if ($Where instanceof Where) {
            $WhereFragment->setWhere($Where);
        }
        
        return $WhereFragment;
    }
}