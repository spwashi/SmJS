<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 11:23 AM
 */

namespace Sm\Communication\Routing\Module;


use Sm\Communication\CommunicationLayer;

class RoutingModuleTest extends \PHPUnit_Framework_TestCase {
    /** @var  CommunicationLayer $communicationLayer */
    public $communicationLayer;
    public function setUp() {
        $routingModule            = new StandardRoutingModule;
        $layer                    = new CommunicationLayer;
        $this->communicationLayer = $layer->registerRoutingModule($routingModule);
    }
    public function testCanRegisterRoutes() {
        $this->communicationLayer->registerRoutes([]);
    }
}
