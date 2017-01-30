<?php
/**
 * User: spwashi2
 * Date: 1/26/2017
 * Time: 2:47 PM
 */

namespace SmTest\Resolvable;


use Sm\Resolvable\Resolvable;
class ResolvableTest extends \PHPUnit_Framework_TestCase {
	public function testCanCreate() {
		$Resolvable = $this->getMockForAbstractClass(Resolvable::class, [null]);
		$this->assertInstanceOf(Resolvable::class, $Resolvable);
		return $Resolvable;
	}
	/**
	 * @param Resolvable $Resolvable
	 * @depends testCanCreate
	 */
	public function testCanReset($Resolvable) {
		$Resolvable->reset();
	}

	/**
	 * @depends testCanCreate
	 * @expectedException \Sm\Resolvable\Error\UnresolvableError
	 * @param Resolvable $Resolvable
	 */
	public function testCanResolveCorrectly($Resolvable) {
		$Resolvable->resolve();
	}
}
