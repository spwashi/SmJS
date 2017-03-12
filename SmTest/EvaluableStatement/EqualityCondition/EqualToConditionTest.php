<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 2:42 PM
 */

namespace Sm\EvaluableStatement\EqualityCondition;


class EqualToConditionTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolve() {
        $EvaluableStatement = EqualToCondition::init(2, 2);
        $this->assertEquals(true, $EvaluableStatement->resolve());
        
        $EvaluableStatement->getVariables();
    }
}
