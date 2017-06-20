<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Communication\Request;

use Sm\Application\App;
use Sm\Communication\Response\Response;
use Sm\Core\Resolvable\FunctionResolvable;

/**
 * Class Request
 *
 * Class that is meant to be representative of whatever the client would request
 *
 * @package Sm\Communication\Request
 */
class Request implements \JsonSerializable {
    protected $url                    = '*';
    protected $path                   = '*';
    protected $method                 = null;
    protected $requested_content_type = Response::TYPE_TEXT_HTML;
    /** @var App $App */
    protected $App = null;
    /** @var null|FunctionResolvable $ChangePathResolvable */
    protected $ChangePathResolvable = null;
    
    public function __toString() {
        return json_encode($this);
    }
    function jsonSerialize() {
        return [ '_type' => 'Request' ];
    }
    /**
     * @return App|null
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param App $App
     *
     * @return Request
     */
    public function setApp(App $App): Request {
        $this->App = $App;
        return $this;
    }
    public static function init($item = null) {
        if ($item instanceof Request) return $item;
        return new static;
    }
}