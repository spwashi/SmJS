<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:54 PM
 */

namespace Sm\Storage\Source\Database;


use Sm\Storage\Source\Source;

abstract class DatabaseSource extends Source {
    protected $database_name;
    
    /**
     * Get the name of the database
     *
     * @return mixed
     */
    public function getName() {
        return $this->database_name;
    }
    public function setModule() { }
}