<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:54 PM
 */

namespace Sm\Storage\Database;


use Sm\Data\Source\DataSource;

abstract class DatabaseDataSource extends DataSource {
    protected $database_name;
    
    /**
     * Get the name of the database
     *
     * @return mixed
     */
    public function getName() {
        return $this->database_name;
    }
    public function getConnection() {
        return isset($this->Authentication) ? $this->Authentication->getConnection() : null;
    }
}