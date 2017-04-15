<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 8:38 PM
 */

namespace Sm\Query;


use Sm\App\App;
use Sm\Entity\EntityType;
use Sm\Entity\EntityTypeMeta;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\MySql\Interpreter\MysqlQueryInterpreter;
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;

class QueryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @return EntityType[]
     */
    public function createEntityTypes() {
        $EntityTypes = [];
        $properties  = [
            'Section'    => [
                'table_name' => 'sections',
                'properties' => [
                    'id',
                    'title',
                    'content',
                ],
            ],
            'Collection' => [
                'table_name' => 'collections',
                'properties' => [
                    'id',
                    'title',
                    'content',
                ],
            ],
            'Colln'      => [
                'table_name' => 'collections',
                'properties' => [
                    'id',
                    'title',
                    'content',
                ],
            ],
        ];
    
        $TableSources = [];
        
        foreach ($properties as $_name => $info) {
            $properties        = $info['properties'];
            $table_name        = $info['table_name'];
            $PropertyContainer = PropertyContainer::init();
    
            $TableSource =
            $TableSources[ $table_name ] =
                $TableSources[ $table_name ] ?? TableSource::init($this->getDatabaseSource(), $table_name);
            
            foreach ($properties as $property) {
                $PropertyContainer->$property = Property::init()->setSource($TableSource);
            }
            $ET = $EntityTypes[ $_name ]
                = new EntityType(EntityTypeMeta::init()->setProperties($PropertyContainer)->setName($_name));
    
            $ET->IdentifyingConditionFactory->register(MysqlQueryInterpreter::class, function ($QueryInterpreter, Query $Query, $ET) {
                $Query->select($ET->Properties->id)->where(Where::equals_($ET->Properties->id, $ET->Properties->id->value));
            });
            
        }
        
        return $EntityTypes;
        
    }
    public function testSyntax() {
        $ET         = $this->createEntityTypes();
        $Section    = $ET['Section'];
        $Collection = $ET['Collection'];
        $Colln      = $ET['Colln'];
        
        $App                  = App::init()->setName('ExampleApp');
        $App->Paths->app_path = BASE_PATH . 'SmTest/ExampleApp/';
        $App->Modules->_app   = include APP_MODULE ??[];
        $Collection->id       = 'collection_id';
        $Section->id          = '1';
        $Section->title       = 'hello';
        $id                   = clone  $Section->Meta->Properties;
    
    
        $WhereClause = Where::greater_(6, $Colln->title)->or_($Collection->id);
    
        $results = $App->Query->select($Colln->title, $Collection->Properties)
                              ->where($WhereClause)
                              ->run();
    
    
        $results = $App->Query->insert($Section->Properties)
                              ->values([ 1, 2, 'This is a test' ], [ 1, 2, 3, 4 ])
                              ->run();
        
    }
    protected function getDatabaseSource() {
        if (isset($this->DatabaseSource)) {
            return $this->DatabaseSource;
        }
        $Authentication = MysqlPdoAuthentication::init()
                                                ->setCredentials('codozsqq',
                                                                 '^bzXfxDc!Dl6',
                                                                 'localhost',
                                                                 'factshift');
        $Authentication->connect();
        $DatabaseSource = MysqlDatabaseSource::init();
        $DatabaseSource->authenticate($Authentication);
        return $this->DatabaseSource = $DatabaseSource;
    }
}
