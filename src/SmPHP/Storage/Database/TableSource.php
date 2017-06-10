<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Storage\Database;


use Sm\Authentication\Authentication;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Source\DataSource;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @property-read \Sm\Storage\Database\ColumnContainer $Columns
 *
 * @method static TableSource init(DatabaseDataSource $DatabaseSource, string $table_name = null)
 * @package Sm\Storage\Database
 */
class TableSource extends DatabaseDataSource {
    protected $table_name;
    /** @var  DatabaseDataSource $DatabaseSource */
    protected $DatabaseSource;
    /** @var PropertyContainer $_Columns */
    protected $_Columns;
    public function __construct(DatabaseDataSource $DatabaseSource, $table_name) {
        $this->DatabaseSource = $DatabaseSource;
        $this->table_name     = $table_name;
        $this->_Columns       = ColumnContainer::init()->setSource($this);
        parent::__construct();
    }
    public function __get($name) {
        if ($name === 'Columns') return $this->_Columns;
        return null;
    }
    public function authenticate(Authentication $authentication = null) {
        $this->DatabaseSource->authenticate($authentication);
        return $this;
    }
    public function isAuthenticated() { return $this->DatabaseSource->isAuthenticated(); }
    public function getRootSource(): DataSource {
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