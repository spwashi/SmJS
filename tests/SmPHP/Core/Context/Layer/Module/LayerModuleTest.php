<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 5:29 PM
 */

namespace Sm\Core\Context\Layer\Module;


use Sm\Core\Context\Context;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Hook\HookContainer;

class LayerModuleTest extends \PHPUnit_Framework_TestCase {
    public function testCanOnlyRegisterLayeras() {
        $layerModule = $this->getMockForAbstractClass(LayerModule::class);
        $layerModule->method('getHookContainer')->willReturn(HookContainer::init());
        
        $layer = $this->getMockForAbstractClass(Layer::class);
        $layerModule->initialize($layer);
        $this->expectException(InvalidContextException::class);
        $layerModule->initialize($this->createMock(Context::class));
    }
}
