<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Request;

use Sm\Error\UnimplementedError;

/**
 * Class Request
 *
 * Class that is meant to be representative of whatever the client would request
 *
 * @package Sm\Request
 */
class Request implements \Sm\Abstraction\Request\Request {
    protected $url    = null;
    protected $path   = null;
    protected $method = null;
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
    public static function init() { return new static; }
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