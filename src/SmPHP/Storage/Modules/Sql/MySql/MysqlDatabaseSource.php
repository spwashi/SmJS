<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:53 PM
 */

namespace Sm\Storage\Modules\Sql\MySql;


use Sm\Authentication\Authentication;
use Sm\Authentication\Error\InvalidAuthenticationError;
use Sm\Storage\Database\DatabaseDataSource;

/**
 * Class MysqlDatabaseSource
 *
 * Represents a Database connection to a MySql Database
 *
 * @package Sm\Storage\Modules\Sql\MySql
 * @method \PDO getConnection()
 */
class MysqlDatabaseSource extends DatabaseDataSource {
    /**
     * @param \Sm\Authentication\Authentication|null $Authentication
     *
     * @return mixed
     * @throws \Sm\Authentication\Error\InvalidAuthenticationError
     */
    public function authenticate(Authentication $Authentication = null) {
        if (!($Authentication instanceof MysqlPdoAuthentication)) {
            throw new InvalidAuthenticationError("Must authenticate using MySql PDO.");
        }
        $this->database_name = $this->database_name ?? $Authentication->getDatabaseName();
        return parent::authenticate($Authentication);
    }
    public function isAuthenticated() {
        return isset($this->Authentication) && $this->Authentication->isValid();
    }
}