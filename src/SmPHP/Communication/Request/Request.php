<?php
/**
 * User: Sam Washington
 * Date: 1/29/17
 * Time: 10:53 PM
 */

namespace Sm\Communication\Request;

/**
 * Class Request
 *
 * Class that is meant to be representative of whatever the client would request
 *
 * @package Sm\Communication\Request
 */
abstract class Request implements \JsonSerializable {
    public static function init($item = null) {
        if ($item instanceof Request) return $item;
        return new static;
    }
}