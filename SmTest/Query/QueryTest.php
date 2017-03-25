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
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;
use Sm\Storage\Modules\Sql\MySql\MysqlQueryInterpreter;
use Sm\Storage\Source\Database\TableSource;

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
    
            $ET->IdentifyingConditionFactory->register(function ($classname, $ET) {
                return [
                    'Condition' => Where::equals_($ET->Properties->id, $ET->Properties->id->value),
                    'Items'     => [ $ET->Properties->id ],
                ];
            }, MysqlQueryInterpreter::class);
            
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
        $id                   = clone  $Section->Meta->Properties;
    
        $WhereClause = Where::greater_(6, 1)
                            ->or_($Section->id);
    
        $results = $App->Query->select($Section->title)
                              ->where($WhereClause)->run();
    
    
        $results = $App->Query->insert($Section->title, $Section->alias)->run();
        
    }
    protected function getDatabaseSource() {
        if (isset($this->DatabaseSource)) return $this->DatabaseSource;
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
