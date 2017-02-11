<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:18 PM
 */

namespace Sm\Storage\Query;


abstract class Query {
    abstract public function select($what);
    abstract public function where();
    abstract public function source();
}