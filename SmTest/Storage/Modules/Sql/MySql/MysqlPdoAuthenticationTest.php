<?php
/**
 * User: Sam Washington
 * Date: 3/13/17
 * Time: 12:09 AM
 */

namespace Sm\Storage\Modules\Sql\MySql;


class MysqlPdoAuthenticationTest extends \PHPUnit_Framework_TestCase {
    /** @var  \Sm\Storage\Modules\Sql\MySql\MysqlPdoAuthentication $MysqlPdoAuthentication */
    protected $MysqlPdoAuthentication;
    public function setUp() {
        $this->MysqlPdoAuthentication = new MysqlPdoAuthentication;
    }
    
    public function testConnection() {
        $this->MysqlPdoAuthentication->setCredentials('codozsqq', '^bzXfxDc!Dl6', 'localhost', 'factshift');
        $result = $this->MysqlPdoAuthentication->connect();
        $this->assertTrue($result);
    }
}
