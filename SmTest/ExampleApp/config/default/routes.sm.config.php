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
    [ 'test' => '#Home::item', ],
    [ '{method}' => '#Home::test', ],
    [ 'Sm/ea/Hello' => StringResolvable::coerce("HELLO"), ],
    [ 'Hello' => StringResolvable::coerce("Hey there!"), ],
    [
        '$' =>
            FunctionResolvable::init(function (Request $Request = null) {
                return 'Hello!!';
            }),
    ],
];