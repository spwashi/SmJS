<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 7:45 AM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\Context;
use Sm\Core\Context\Context_basedProxy;

/**
 * Class ModuleProxy
 *
 * Proxy for Modules
 *
 * @package Sm\Core\Module
 */
class ModuleProxy implements Context_basedProxy {
    /** @var \Sm\Core\Context\Context $context The Context that this Proxy will pass into the Module */
    protected $context;
    /** @var \Sm\Core\Module\Module $module The module being proxied */
    protected $module;
    /**
     * ModuleProxy constructor.
     *
     *
     * @param \Sm\Core\Module\Module   $module
     * @param \Sm\Core\Context\Context $context
     */
    public function __construct(Module $module, Context $context) {
        $this->context = $context;
        $this->module  = $module;
    }
    /**
     * Get the Context that the Proxy is referencing
     *
     * @return \Sm\Core\Context\Context
     */
    public function getContext(): Context {
        return $this->context;
    }
}