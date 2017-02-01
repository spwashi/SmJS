<?php
/**
 * User: Sam Washington
 * Date: 1/31/17
 * Time: 4:11 PM
 */
use Sm\Request\Request;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\StringResolvable;

return [
    [
        'localhost/Sm/fs/Hello' => StringResolvable::coerce("HELLO"),
    ],
    [
        'Hello' => StringResolvable::coerce("Hey there!"),
    ],
    [
        'localhost/Sm/fs$' => FunctionResolvable::init(function (Request $Request) {
            return ($Request);
        }),
    ],
];