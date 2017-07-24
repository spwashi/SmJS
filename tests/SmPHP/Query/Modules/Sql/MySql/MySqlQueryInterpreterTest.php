<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 8:37 PM
 */

namespace Sm\Query\Modules\Sql\MySql;


use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatter;
use Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory;
use Sm\Query\Modules\Sql\MySql\Authentication\MySqlAuthentication;
use Sm\Query\Modules\Sql\SqlExecutionContext;

class MySqlQueryInterpreterTest extends \PHPUnit_Framework_TestCase {
    public function getAuthentication(): MySqlAuthentication {
        #todo lol remove this from this file, y'goof!
        return MySqlAuthentication::init()->setCredentials("codozsqq", "^bzXfxDc!Dl6", "localhost", "factshift");
    }
    public function testCanSelect() {
        /** @var SqlQueryFormatter $sqlQueryFormatter */
        $sqlQueryFormatter = $this->getMockForAbstractClass(SqlQueryFormatter::class,
                                                            [
                                                                new SqlQueryFormatterFactory(new SqlFormattingProxyFactory,
                                                                                             new SqlFormattingAliasContainer,
                                                                                             new SqlExecutionContext),
                                                            ]);
        $interpreter       = new MySqlQueryInterpreter($this->getAuthentication(),
                                                       $sqlQueryFormatter);
        
        $result = $interpreter->interpret("SELECT 'hello' as test;");
        $this->assertInternalType('array', $result);
        $this->assertEquals('hello',
                            $result['test']??null);
    }
}
