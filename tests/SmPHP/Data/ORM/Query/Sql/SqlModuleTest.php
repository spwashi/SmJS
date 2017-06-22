<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 11:46 AM
 */

namespace Sm\Process\Query\Sql;


use Sm\Application\App;
use Sm\Storage\Database\DatabaseDataSource;

class SqlModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  App $App */
    protected $App;
    public function setUp() {
        $this->App  = App::init();
        $sql_module = SM_PATH . '/Storage/Modules/Sql/MySql/mysql.sql.sm.module.php';
        
        if (!is_file($sql_module)) return;
        
        $this->App->Modules->sql = include $sql_module ?? [];
    }
    public function testCanGetDatabaseSource() {
        $this->assertInstanceOf(DatabaseDataSource::class,
                                $this
                                    ->App
                                    ->Modules
                                    ->sql
                                    ->getDatabaseSource());
    
        $this->assertInstanceOf(DatabaseDataSource::class,
                                $this
                                    ->App
                                    ->Modules
                                    ->sql
                                    ->DatabaseSource);
    }
    public function testCanDispatch() {
        $this
            ->App
            ->Modules
            ->sql
            ->dispatch();
        $this->assertTrue(true);
    }
}
