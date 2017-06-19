<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:50 PM
 */

namespace Sm\Data\Property;


use Sm\Core\Resolvable\StringResolvable;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource;
use Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication;

class PropertyTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Data\Property\Property $Property */
    protected $Property;
    public function setUp() {
        $this->Property = new Property;
    }
    public function testCanCreate() {
        $Property = new Property();
        $Property = Property::init();
        $this->assertInstanceOf(Property::class, $Property);
    }
    public function testCanMarkReadonlyAndNotSet() {
        $this->Property->markReadonly();
        $this->expectException(ReadonlyPropertyException::class);
        $this->testCanSetValue();
    }
    public function testCanSetValue() {
        $this->Property->value = 'sam';
        $this->assertEquals('sam', $this->Property->value);
        $this->assertInstanceOf(StringResolvable::class, $this->Property->raw_value);
    }
    public function testCanUseSource() {
        # ~cringes~
        #   This should NOT be here, boy
        $Authentication = MysqlPdoAuthentication::init()->setCredentials('codozsqq',
                                                                         '^bzXfxDc!Dl6',
                                                                         'localhost',
                                                                         'factshift');
        $DatabaseSource = MysqlDatabaseSource::init();
        $DatabaseSource->authenticate($Authentication);
        
        $Source           = new TableSource($DatabaseSource, 'sections');
        $TitleProperty    = new Property('title', $Source);
        $SubtitleProperty = new Property('subtitle', $Source);
        $ContentProperty  = new Property('content', $Source);
    }
}
