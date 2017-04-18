<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:49 PM
 */

namespace Sm\Storage\Database;


use Sm\Authentication\Authentication;
use Sm\Entity\Property\PropertyContainer;
use Sm\Storage\Source\Source;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

/**
 * Class TableSource
 *
 * Represents a Source from a Table
 *
 * @property-read \Sm\Storage\Database\ColumnContainer $Columns
 *
 * @method static TableSource init(DatabaseSource $DatabaseSource, string $table_name = null)
 * @package Sm\Storage\Database
 */
class TableSource extends DatabaseSource {
    protected $table_name;
    /** @var  DatabaseSource $DatabaseSource */
    protected $DatabaseSource;
    /** @var PropertyContainer $_Columns */
    protected $_Columns;
    public function __construct(DatabaseSource $DatabaseSource, $table_name) {
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