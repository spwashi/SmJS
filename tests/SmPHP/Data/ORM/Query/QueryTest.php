<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 8:38 PM
 */

namespace Sm\Data\Query;


use Sm\Core\Resolvable\ArrayResolvable;
use Sm\Data\Datatype\Integer_;
use Sm\Data\Datatype\Null_;
use Sm\Data\Datatype\String_;
use Sm\Data\ORM\EntityType\EntityType;
use Sm\Data\ORM\EntityType\EntityTypeMeta;
use Sm\Data\Property\Property;
use Sm\Data\Property\PropertyContainer;
use Sm\Storage\Database\TableSource;
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
            
        }
        
        return $EntityTypes;
        
    }
    public function testNothing() {
        $this->assertTrue(true);
    }
    public function deprecated_testSyntax() {
        $ET         = $this->createEntityTypes();
        $Section    = $ET['Section'];
        $Collection = $ET['Collection'];
        $Colln      = $ET['Colln'];
        
        
        $Collection->id = 'collection_id';
        $Section->id    = '1';
        $Section->title = 'hello';
        $id             = clone  $Section->Meta->Properties;
        
        
        $SectionTypesTable                        = TableSource::init($this->getDatabaseSource(), 'section_types');
        $SectionTypesTable->Columns->id           = Property::init()->setMaxLength(4)->setPotentialTypes(Integer_::class);
        $SectionTypesTable->Columns->primary_keys = 'id';
        
        $SectionsTable                        = TableSource::init($this->getDatabaseSource(), 'sections');
        $SectionsTable->Columns->primary_keys = 'id';
        $SectionsTable->Columns->id           = Property::init()->setMaxLength(10)->setPotentialTypes(Integer_::class);
        $SectionsTable->Columns->content      = Property::init()->setMaxLength(75)->setPotentialTypes(Null_::class, String_::class)->setDefault('title');
        $SectionsTable->Columns->title        = Property::init()->setMaxLength(25)->setPotentialTypes(Null_::class, String_::class)->setDefault('title');
        $SectionsTable->Columns->section_type = Property::init()
                                                        ->setPotentialTypes(Null_::class, Integer_::class)
                                                        ->setDefault(4);
    
        $Query   = Query::init();
        $results = $Query->create($SectionTypesTable)->run();
        $results = $Query->create($SectionsTable)->run();
    
        $results = $Query->select($Colln->title, $Collection->Properties)
                         ->where(WhereClause::greater_(6, $Colln->title)->or_($Collection->id))
                         ->run();
    
        $results = $Query->update($Colln->title, $Collection->Properties)
                         ->where(WhereClause::greater_(7, $Colln->title)->or_($Collection->id))
                         ->run();
    
    
        $results = $Query->insert($Section->Properties)
                         ->values([ 1, 2, 'This is a test' ],
                                  [ 'this', 'is', ArrayResolvable::init([ 'a', 'set', 'of' => 'arrays' ]) ],
                                  [ 1, 2, 3, 4 ])
                         ->run();
    
        $results = $Query->delete($Section->Properties)->run();
        
    }
    protected function getDatabaseSource() {
        if (isset($this->DatabaseSource)) {
            return $this->DatabaseSource;
        }
        $Authentication = MysqlPdoAuthentication::init()
                                                ->setCredentials('codozsqq',
                                                                 '^bzXfxDc!Dl6',
                                                                 'localhost',
                                                                 'test');
        $Authentication->connect();
        $DatabaseSource = MysqlDatabaseSource::init();
        $DatabaseSource->authenticate($Authentication);
        return $this->DatabaseSource = $DatabaseSource;
    }
}
