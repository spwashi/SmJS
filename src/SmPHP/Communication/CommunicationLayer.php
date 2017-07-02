<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:35 PM
 */

namespace Sm\Communication;


use Sm\Communication\Routing\Module\RoutingModule;
use Sm\Core\Context\Layer\Module\Exception\MissingModuleException;
use Sm\Core\Context\Layer\StandardLayer;

/**
 * Class CommunicationLayer
 *
 * Layer responsible for inter-service communication.
 * Modules for Routing and Dispatching Requests.
 *
 * @package Sm\Communication
 */
class CommunicationLayer extends StandardLayer {
    const ROUTING_MODULE = 'routing';
    /**
     * @param \Sm\Communication\Routing\Module\RoutingModule|\Sm\Core\Module\ModuleProxy $routingModule
     *
     * @return  static
     * */
    public function registerRoutingModule(RoutingModule $routingModule) {
        return $this->registerModule(static::ROUTING_MODULE, $routingModule);
    }
    public function registerRoutes($request) {
        $routingModule = $this->getRoutingModule();
        return $routingModule->registerRoutes($request);
    }
    public function route($request) {
        $routingModule = $this->getRoutingModule();
        if (!$routingModule) throw new MissingModuleException("Missing a Routing Module");
        return $routingModule->route($request);
    }
    /**
     * Get the Module used for Routing
     *
     * @return null|\Sm\Communication\Routing\Module\RoutingModule|\Sm\Core\Module\Module
     * @throws \Sm\Core\Context\Layer\Module\Exception\MissingModuleException
     */
    protected function getRoutingModule(): RoutingModule {
        $routingModule = $this->getModule(CommunicationLayer::ROUTING_MODULE);
        if (!$routingModule) throw new MissingModuleException("Missing a Routing Module");
        return $routingModule;
    }
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [ CommunicationLayer::ROUTING_MODULE ];
    }
}