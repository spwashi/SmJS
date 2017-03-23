<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Storage\Source\Database;


use Sm\Authentication\Authentication;
use Sm\Storage\Source\Source;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @method static init(DatabaseSource $DatabaseSource, string $table_name = null)
 * @package Sm\Storage\Source\Database
 */
class TableSource extends DatabaseSource {
    protected $table_name;
    /** @var  DatabaseSource $DatabaseSource */
    protected $DatabaseSource;
    public function __construct(DatabaseSource $DatabaseSource, $table_name) {
        $this->DatabaseSource = $DatabaseSource;
        $this->table_name     = $table_name;
        parent::__construct();
    }
    
    public function authenticate(Authentication $authentication = null) {
        $this->DatabaseSource->authenticate($authentication);
        return $this;
    }
    public function isAuthenticated() { return $this->DatabaseSource->isAuthenticated(); }
    public function getRootSource(): Source {
        return $this->DatabaseSource->getRootSource();
    }
    
    /**
     * Get the name of the table
     *
     * @return string
     */
    public function getName() {
        return $this->table_name;
    }
    /**
     * @param mixed $table_name
     *
     * @return TableSource
     */
    public function setTableName($table_name) {
        $this->table_name = $table_name;
        return $this;
    }
}