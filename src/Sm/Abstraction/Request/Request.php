<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 11:15 PM
 */

namespace Sm\Abstraction\Request;

/**
 * Interface Request
 *
 * Represents a request as it gets sent by the browser
 *
 * @package Sm\Abstraction\Request
 */
interface Request {
    public function getUrl();
    public function getUrlPath();
    public function getMethod();
    public function getData();
    public function getBody();
}