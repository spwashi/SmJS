<?php
/**
 * User: Sam Washington
 * Date: 6/19/17
 * Time: 12:38 PM
 */

namespace Sm\Communication\Network\Http;


use Sm\Communication\Request\Request;
use Sm\Communication\Response\Response;

/**
 * Class HttpRequest
 *
 * @package Sm\Communication\Network\Http
 */
class HttpRequest extends Request {
    protected $url                    = '*';
    protected $path                   = '*';
    protected $method                 = null;
    protected $requested_content_type = Response::TYPE_TEXT_HTML;
    /**
     * @param null $url
     *
     * @return \Sm\Communication\Network\Http\HttpRequest|Request
     */
    public static function init($url = null) {
        if (is_string($url)) return (new static)->setUrl($url);
        return parent::init($url);
    }
    /**
     * @return null
     */
    public function getRequestedContentType() {
        return $this->requested_content_type;
    }
    /**
     * @param null $requested_content_type
     *
     * @return Request
     */
    public function setRequestedContentType($requested_content_type) {
        $this->requested_content_type = $requested_content_type;
        return $this;
    }
    
    /**
     * Get the request method used to make this request. Defaults to "get"
     *
     * @return null|string
     */
    public function getRequestMethod() {
        return $this->method ?? 'get';
    }
    /**
     * Set the HTTP Request Method that will be or has been used to make this request
     *
     * @param string $method The request method that will be used or has been used to make this request
     *
     * @return $this
     */
    public function setRequestMethod($method) {
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
        if (is_string($this->path)) {
            $this->path = trim($this->path, '/ ');
        }
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
    public function jsonSerialize() {
        return parent::jsonSerialize() + [
                'url'      => $this->getUrl(),
                'url_path' => $this->getUrlPath(),
                'method'   => $this->getRequestMethod(),
            ];
    }
    
    ##################################
    #
    ##################################
    
    
    /**
     * Get the URL of however we entered
     *
     * @return string
     */
    public static function getEnvironmentRequestURL() {
        $host        = $_SERVER['HTTP_HOST']??'';
        $request_uri = $_SERVER['REQUEST_URI']??'';
        return "//{$host}{$request_uri}";
    }
    /**
     * Get the Request Method that was used to make the request initially
     *
     * @return string
     */
    public static function getEnvironmentRequestMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    /**
     * Initialize an HttpRequest from the Environment
     *
     * @return \Sm\Communication\Network\Http\HttpRequest
     */
    public static function getRequestFromEnvironment() {
        return static::init(static::getEnvironmentRequestURL())
                     ->setRequestMethod(static::getEnvironmentRequestMethod());
    }
}