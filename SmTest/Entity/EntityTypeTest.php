<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 9:56 PM
 */

namespace Sm\Entity;


use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\ReadonlyPropertyException;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;

class EntityTypeTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Entity\EntityType $EntityType */
    protected $EntityType;
    public function setUp() {
        $this->EntityType = $this->getEntityType();
    }
    
    public function testCanSetProperty() {
        $Section             = $this->EntityType;
        $Section->first_name = "hello";
        
        $this->assertEquals("hello", $Section->first_name->value);
    }
    
    public function testCannotSetPropertiesDirectly() {
        $this->expectException(ReadonlyPropertyException::class);
        $this->EntityType->first_name->value = 'sam';
    }
    public function testCannotSetPropertyContainerDirectly() {
        $this->expectException(ReadonlyPropertyException::class);
        $this->EntityType->Properties->first_name = new Property;
    }
    
    public function testCanOwnPropertiesCorrectly() {
        $Section           = $this->EntityType;
        $Section->eg_title = "Samuel";
        $Section->eg_alias = "Washington";
        $this->assertEquals($Section, $Section->eg_alias->getOwners()[0]);
        $Properties = $Section->Properties;
        $this->assertInstanceOf(PropertyContainer::class, $Properties);
        
        $clonedProperties = clone $Properties;
        $this->assertNotEquals($Section, $clonedProperties->getOwner());
        
    }
    
    protected function getEntityType() {
        $property_array = [ 'first_name' => new Property,
                            'last_name'  => new Property,
                            'eg_alias'   => new Property,
                            'eg_title'   => new Property,
                            'alias'      => new Property, ];
        
        $DatabaseSource = $this->getDatabaseSource();
        $TableModel     = TableSource::init($DatabaseSource, 'sections');
        $Properties     = PropertyContainer::init()->register($property_array);
        $Meta           = EntityTypeMeta::init()->setProperties($Properties)->setName('Section');
        $EntityType     = new EntityType($Meta);
        
        foreach ($EntityType->Properties as $index => $item) {
            $item->setSource($TableModel);
        }
        return $EntityType;
    }
    /**
     * @return \Sm\Storage\Database\DatabaseSource
     */
    protected function getDatabaseSource() {
        $Authentication = MysqlPdoAuthentication::init()
                                                ->setCredentials('codozsqq',
                                                                 '^bzXfxDc!Dl6',
                                                                 'localhost',
                                                                 'factshift');
        $DatabaseSource = MysqlDatabaseSource::init();
        $DatabaseSource->authenticate($Authentication);
        return $DatabaseSource;
    }
}
