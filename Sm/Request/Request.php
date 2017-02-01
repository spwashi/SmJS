<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Request;

use Sm\Abstraction\Coercable;
use Sm\App\App;
use Sm\Error\UnimplementedError;

/**
 * Class Request
 *
 * Class that is meant to be representative of whatever the client would request
 *
 * @package Sm\Request
 */
class Request implements \Sm\Abstraction\Request\Request, Coercable, \JsonSerializable {
    protected $url    = null;
    protected $path   = null;
    protected $method = null;
    /** @var App $app */
    protected $app = null;
    public function getBody() {
        throw new UnimplementedError();
    }
    public function getData() {
        throw new UnimplementedError();
    }
    /**
     * Get the request method used to make this request. Defaults to "get"
     *
     * @return null|string
     */
    public function getMethod() {
        return $this->method ?? 'get';
    }
    /**
     * @param string $method The request method that will be used or has been used to make this request
     *
     * @return $this
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }
    /**
     * @return null|string
     */
    public function getUrl() {
        return $this->url;
    }
    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url) {
        $this->url  = $url;
        $parsed     = parse_url($url);
        $this->path = $parsed['path'] ?? $this->url;
        if (is_string($this->path)) $this->path = trim($this->path, '/ ');
        return $this;
    }
    /**
     * Get the part of the URL that isn't the domain or the protocol or stuff
     *
     * @return null
     */
    public function getUrlPath() {
        return $this->path ?? null;
    }
    public function __toString() {
        return json_encode($this);
    }
    function jsonSerialize() {
        return [
            '_type'    => 'Request',
            'url'      => $this->getUrl(),
            'url_path' => $this->getUrlPath(),
            'method'   => $this->getMethod(),
        ];
    }
    /**
     * @return App
     */
    public function getApp(): App {
        return $this->app;
    }
    /**
     * @param App $app
     *
     * @return Request
     */
    public function setApp(App $app): Request {
        $this->app = $app;
        return $this;
    }
    public static function init() { return new static; }
    public static function coerce($item = null) {
        if ($item instanceof Request) return $item;
        if (is_string($item)) {
            return static::init()->setUrl($item);
        } else {
            return new static;
        }
    }
    /**
     * Get the URL of however we entered
     *
     * @return string
     */
    public static function getRequestUrl() {
        return "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
    /**
     * Get the Request Method that was used to make the request initially
     *
     * @return string
     */
    public static function getRequestMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}