<?php
/**
 * User: Sam Washington
 * Date: 6/28/17
 * Time: 11:51 PM
 */

namespace Sm\Core\Context;


class AbstractContextTest extends \PHPUnit_Framework_TestCase {
    public function testObjectIDsAreAllUnique() {
        /** @var \Sm\Core\Context\AbstractContext $abstr_1 */
        $abstr_1 = $this->getMockForAbstractClass(AbstractContext::class);
        /** @var \Sm\Core\Context\AbstractContext $abstr_2 */
        $abstr_2 = $this->getMockForAbstractClass(AbstractContext::class);
        /** @var \Sm\Core\Context\AbstractContext $abstr_3 */
        $abstr_3 = $this->getMockForAbstractClass(AbstractContext::class);
        $this->assertNotEquals($abstr_1->readContextAttributes(), $abstr_2->readContextAttributes());
        $this->assertNotEquals($abstr_3->readContextAttributes(), $abstr_2->readContextAttributes());
        $this->assertNotEquals($abstr_1->readContextAttributes(), $abstr_3->readContextAttributes());
    }
}
