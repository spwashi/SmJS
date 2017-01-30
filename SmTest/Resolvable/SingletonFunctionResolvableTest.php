<?php
/**
 * User: Sam Washington
 * Date: 1/26/17
 * Time: 10:11 AM
 */

namespace SmTest\Resolvable;

use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\SingletonFunctionResolvable;

/**
 * Class SingletonFunctionResolvableTest
 *
 * @package SmTest\Resolvable
 */
class SingletonFunctionResolvableTest extends FunctionResolvableTest {
	public function testCanCreate() {
		$Resolvable = new SingletonFunctionResolvable(function () { });
		$this->assertInstanceOf(SingletonFunctionResolvable::class, $Resolvable);
		return $Resolvable;
	}
	public function genericSubjectProvider() {
		return [
			["test"],
			[1],
			[null],
			[[]],
		];
	}
	public function testOnlyResolvesOnce() {
		$Resolvable = new SingletonFunctionResolvable(function ($subject) { return $subject; });
		$Resolvable->resolve("one");
		$result = $Resolvable->resolve("two");
		$this->assertEquals("one", $result);
	}
	/**
	 * @dataProvider genericSubjectProvider
	 *
	 * @param $subject
	 */
	public function testCanResolveCorrectly($subject) {
		$Resolvable = new SingletonFunctionResolvable(function () use ($subject) { return $subject; });
		$this->assertTrue($subject === $Resolvable->resolve());
	}
}
