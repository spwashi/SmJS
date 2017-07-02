<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 5:18 PM
 */

namespace Sm\Core\Paths;


use Sm\Core\Resolvable\Error\UnresolvableError;

class PathContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanResolvePaths() {
        $pathContainer = new PathContainer();
        $pathContainer->register('test', 'dirt');
        $resolve = $pathContainer->resolve('test');
        $this->assertEquals('dirt/', $resolve);
        
        $this->expectException(UnresolvableError::class);
        PathContainer::init()->resolve('nothing');
    }
}
