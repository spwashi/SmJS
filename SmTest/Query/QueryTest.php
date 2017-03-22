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
                    'title',
                    'first_name',
                    'last_name',
                    'alias',
                ],
            ],
            'Collection' => [
                'table_name' => 'collections',
                'properties' => [
                    'alias',
                    'title',
                    'content',
                ],
            ],
        ];
        
        foreach ($properties as $_name => $info) {
            $properties        = $info['properties'];
            $table_name        = $info['table_name'];
            $PropertyContainer = PropertyContainer::init();
            foreach ($properties as $property) {
                $PropertyContainer->$property = Property::init()->setSource(TableSource::init($this->getDatabaseSource(), $table_name));
            }
            $EntityTypes[ $_name ] = new EntityType(EntityTypeMeta::init()->setProperties($PropertyContainer)->setName($_name));
        }
        
        return $EntityTypes;
        
    }
    public function testSyntax() {
        $ET         = $this->createEntityTypes();
        $Section    = $ET['Section'];
        $Collection = $ET['Collection'];
        
        $App                  = App::init()->setName('ExampleApp');
        $App->Paths->app_path = BASE_PATH . 'SmTest/ExampleApp/';
        $App->Modules->_app   = include APP_MODULE ??[];
        
        
        $Query = Query::select_($Collection->title,
                                $Section->Properties)
                      ->setFactoryContainer($App->Factories);
        
        
        $Query->run();
    }
    protected function getDatabaseSource() {
        $Authentication = MysqlPdoAuthentication::init()
                                                ->setCredentials('codozsqq',
                                                                 '^bzXfxDc!Dl6',
                                                                 'localhost',
                                                                 'factshift');
        $Authentication->connect();
        $DatabaseSource = MysqlDatabaseSource::init();
        $DatabaseSource->authenticate($Authentication);
        return $DatabaseSource;
    }
}
