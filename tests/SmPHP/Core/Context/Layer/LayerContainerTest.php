<?php
/**
 * User: Sam Washington
 * Date: 6/30/17
 * Time: 11:09 PM
 */

namespace Sm\Core\Context\Layer;


use PHPUnit_Framework_MockObject_MockObject;
use Sm\Core\Exception\InvalidArgumentException;

class LayerContainerTest extends \PHPUnit_Framework_TestCase {
    public function testCanRegister() {
        $layerContainer = new LayerContainer();
        /** @var PHPUnit_Framework_MockObject_MockObject|\Sm\Core\Context\Layer\Layer $layer */
        $layer = $this->getMockForAbstractClass(Layer::class);
        $layerContainer->register('test_layer_1', $layer);
        $this->assertInstanceOf(Layer::class, $layerContainer->resolve('test_layer_1'));
        
        $this->expectException(InvalidArgumentException::class);
        $layerContainer->register('test', 'Not a Layer');
    }
}
