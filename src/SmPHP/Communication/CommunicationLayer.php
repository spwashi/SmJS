<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:35 PM
 */

namespace Sm\Communication;


use Sm\Core\Context\Layer\Layer;
use Sm\Core\Module\Module;

/**
 * Class CommunicationLayer
 *
 * Layer responsible for inter-service communication.
 * Modules for Routing and Dispatching Requests.
 *
 * @package Sm\Communication
 */
class CommunicationLayer extends Layer {
    const ROUTING_MODULE = 'routing';
    public function registerRoutingModule(Module $routingModule) {
        $this->ModuleContainer->register(static::ROUTING_MODULE, $routingModule);
    }
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    protected function _listExpectedModules(): array {
        return [ CommunicationLayer::ROUTING_MODULE ];
    }
}