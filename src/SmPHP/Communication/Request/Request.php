<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Communication\Request;

use Sm\Communication\Response\Response;
use Sm\Core\Application\App;
use Sm\Core\Error\UnimplementedError;
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
     * Set the HTTP Request Method that will be or has been used to make this request
     *
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
        if ($this->ChangePathResolvable) {
            return $this->ChangePathResolvable->resolve($this->path);
        }
        return $this->path ?? null;
    }
    /**
     * Set a function that changes the UrlPath when we "get" it
     *
     * If we set this to be a string, we will just remove that string from the url path.
     * This is useful for when we want all of an app's url paths to be relative to something we strip
     * from the URL
     *
     * @see Request::getUrlPath
     *
     *
     * @param FunctionResolvable|string $functionResolvable ($url):string
     *
     * @return $this
     */
    public function setChangePath($functionResolvable) {
        if (is_string($functionResolvable)) {
            $functionResolvable = FunctionResolvable::coerce(function ($path) use ($functionResolvable) {
                return preg_replace("~({$functionResolvable})/?~", '', $path);
            });
        }
        $this->ChangePathResolvable = $functionResolvable;
        return $this;
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
    public static function init() { return new static; }
    public static function coerce($item = null) {
        if ($item instanceof Request) {
            return $item;
        }
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
        $host        = $_SERVER['HTTP_HOST']??'';
        $request_uri = $_SERVER['REQUEST_URI']??'';
        return "//{$host}{$request_uri}";
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