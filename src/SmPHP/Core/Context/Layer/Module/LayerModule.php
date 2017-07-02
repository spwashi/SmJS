<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:47 AM
 */

namespace Sm\Core\Context\Layer\Module;

use Sm\Core\Context\Context;
use Sm\Core\Context\Exception\InvalidContextException;
use Sm\Core\Context\Layer\Layer;
use Sm\Core\Module\AbstractModule;
use Sm\Core\Module\ModuleProxy;

/**
 * Class LayerModule
 *
 * @package Sm\Core\Context\Layer
 */
abstract class LayerModule extends AbstractModule {
    protected function createModuleProxy(Context $context): ModuleProxy {
        return new LayerModuleProxy($this, $context);
    }
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return bool|null
     * @throws \Sm\Core\Context\Exception\InvalidContextException
     */
    protected function _check(Context $context) {
        if (!($context instanceof Layer)) throw new InvalidContextException("Can only interact with this Module within a Layer Context");
        return parent::_check($context);
    }
    
}