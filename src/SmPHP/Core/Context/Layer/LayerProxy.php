<?php
/**
 * User: Sam Washington
 * Date: 6/29/17
 * Time: 12:47 AM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Proxy\Proxy;

/**
 * Class LayerProxy
 *
 * Proxy for Layer Classes
 *
 * @package Sm\Core\Context\Layer
 */
class LayerProxy implements Proxy {
    /** @var \Sm\Core\Context\Layer\Layer $layer The layer being proxied */
    protected $layer;
    /** @var \Sm\Core\Context\Layer\LayerRoot The LayerRoot that the proxy will do all actions relative to */
    protected $layerRoot;
    /**
     * LayerProxy constructor.
     *
     * @param \Sm\Core\Context\Layer\Layer     $layer     The Layer being proxied
     * @param \Sm\Core\Context\Layer\LayerRoot $layerRoot The LayerRoot using this Proxy
     */
    public function __construct(Layer $layer, LayerRoot $layerRoot) {
        $this->layer     = $layer;
        $this->layerRoot = $layerRoot;
    }
}