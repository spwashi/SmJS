<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 3:12 PM
 */

namespace Sm\Communication\Routing;


use Sm\Core\Context\Layer\Module\LayerModule;
use Sm\Core\Hook\HookContainer;

/**
 * Class RoutingModule
 *
 * Module that will be used to execute some functionality based on a Request, usually returning a Response
 *
 * @package Sm\Communication\Routing
 */
class RoutingModule extends LayerModule {
    /** @var \Sm\Core\Hook\HookContainer $hookContainer */
    protected $hookContainer;
    
    public function __construct() {
        $this->hookContainer = new HookContainer;
    }
    /**
     * Get the Hooks held by this class in a HookContainer
     *
     * @return null|\Sm\Core\Hook\HookContainer
     */
    public function getHookContainer(): ?HookContainer {
        return $this->hookContainer;
    }
    
}