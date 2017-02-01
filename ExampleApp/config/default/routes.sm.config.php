<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 4:11 PM
 */
use Sm\Request\Request;
use Sm\Resolvable\FunctionResolvable;

return [
    [ 'localhost/Sm/fs' =>
          FunctionResolvable::init(function (Request $Request) {
              return ($Request);
          }),
    ],
];