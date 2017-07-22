<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:54 PM
 */

namespace Sm\Storage\Database;


use Sm\Authentication\Authentication;
use Sm\Data\Source\DataSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;

/**
 * Class DatabaseDataSource
 *
 * @package Sm\Storage\Database
 */
class DatabaseDataSource extends DataSource implements NamedDataSourceSchema {
    protected $name;
    /**
     * DatabaseDataSource constructor.
     *
     * @param \Sm\Authentication\Authentication|null $Authentication The thing that will hold a reference to the connection
     * @param string                                 $name
     */
    public function __construct(Authentication $Authentication = null, string $name = null) {
        if (isset($Authentication)) $this->authentication = $Authentication;
        $this->name = $name;
        parent::__construct();
    }
    /**
     * Get the name of the database
     *
     * @return mixed
     */
    public function getName(): ?string {
        return $this->name;
    }
    public function getConnection() {
        return isset($this->authentication) ? $this->authentication->getConnection() : null;
    }
}