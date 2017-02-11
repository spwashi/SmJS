<?php
/**
 * User: Sam Washington
 * Date: 2/8/17
 * Time: 10:34 PM
 */

namespace Sm\Output;


use Sm\Response\Response;

class Output {
    
    /**
     * Given a response of some sort, echo an output to the screen
     *
     * @param $response
     */
    public static function out($response) {
        if ($response instanceof Response) {
            $content_type = $response->getContentType();
        } else {
            $content_type = Response::TYPE_JSON;
            $response     =
                is_scalar($response) ||
                is_array($response) ||
                is_bool($response) ||
                is_null($response)
                    ? Response::coerce($response)
                    : null;
        }
        
        $body = $response->resolve();
        header("Content-Type: {$content_type}");
        echo $body;
    }
}