<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Storage\Database;


use Sm\Authentication\Authentication;
use Sm\Data\Property\PropertyContainer;
use Sm\Data\Source\DataSource;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @property-read \Sm\Storage\Database\ColumnContainer $columns
 *
 * @method static TableSource init(DatabaseDataSource $DatabaseSource, string $table_name = null)
 * @package Sm\Storage\Database
 */
class TableSource extends DataSource {
    protected $table_name;
    /** @var  DatabaseDataSource $databaseSource */
    protected $databaseSource;
    /** @var PropertyContainer $columnContainer */
    protected $columnContainer;
    
    public function __construct(DatabaseDataSource $DatabaseSource, $table_name) {
        $this->databaseSource  = $DatabaseSource;
        $this->table_name      = $table_name;
        $this->columnContainer = ColumnContainer::init()->setSource($this);
        parent::__construct();
    }
    public function __get($name) {
        if ($name === 'columns') return $this->columnContainer;
        return null;
    }
    public function isAuthenticated() { return $this->databaseSource->isAuthenticated(); }
    public function getRootSource(): DataSource {
        return $this->databaseSource->getRootSource();
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
    protected function authenticate(Authentication $authentication = null) {
        $this->databaseSource->authenticate($authentication);
        return $this;
    }
}