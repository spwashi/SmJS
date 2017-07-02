<?php
/**
 * User: Sam Washington
 * Date: 6/30/17
 * Time: 10:49 PM
 */

namespace Sm\Core\Context\Layer;


use PHPUnit_Framework_MockObject_MockObject;
use Sm\Core\Internal\Identification\Identifier;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;

class LayerTest extends \PHPUnit_Framework_TestCase {
    public function testCanInitialize() {
        /** @var \Sm\Core\Context\Layer\Layer|PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockForAbstractClass(Layer::class);
        $mock->method('_listExpectedModules')->willReturn([ 'test_module_1' ]);
        
        /** @var \Sm\Core\Context\Layer\LayerRoot|PHPUnit_Framework_MockObject_MockObject $layerRoot */
        $layerRoot = $this->createMock(LayerRoot::class);
        $layerRoot->method('getObjectId')->willReturn(Identifier::generateIdentity($layerRoot));
        return $mock->initialize($layerRoot);
    }
    public function testCanRegisterModule() {
        /** @var \Sm\Core\Context\Layer\Layer|PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this->getMockForAbstractClass(Layer::class);
        $mock->method('_listExpectedModules')->willReturn([ 'test_module_1' ]);
        /** @var Module $module */
        $module = $this->createMock(Module::class);
        
        $mock->registerModule('test_module_1', $module);
        $this->expectException(InvalidModuleException::class);
        $mock->registerModule('test_module_2', $module);
    }
}
