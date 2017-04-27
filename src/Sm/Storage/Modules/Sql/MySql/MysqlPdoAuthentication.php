<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 11:55 PM
 */

namespace Sm\Storage\Modules\Sql\MySql;


use Sm\Authentication\PasswordAuthentication;

class MysqlPdoAuthentication extends PasswordAuthentication {
    /** @var  string $database_name */
    protected $database_name;
    /** @var  string $host */
    protected $host;
    
    public function setCredentials($username = null, $password = null, $host = null, $database = null) {
        parent::setCredentials($username, $password);
        if (isset($host)) {
            $this->host = $host;
        }
        if (isset($database)) {
            $this->database_name = $database;
        }
        return $this;
    }
    
    /**
     * Get then Host name that we will use to connect
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }
    /**
     * Get the name of the database we're using to connect
     *
     * @return string
     */
    public function getDatabaseName() {
        return $this->database_name;
    }
    
    /**
     * Is the authentication still valid?
     *
     * @return bool
     */
    public function isValid(): bool {
        return isset($this->connection);
    }
    /**
     * Connect to the Authentication using the available credentials
     *
     * @return mixed
     */
    public function connect() {
        $dsn              = "mysql:host=" . $this->host . ";dbname=" . $this->database_name . ';charset=utf8';
        $username         = $this->getUsername();
        $password         = $this->getPassword();
        $this->connection = new \PDO($dsn, $username, $password);
        
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        
        return $this->isValid();
    }
    
    /**
     * Get the Connection used by the Authentication
     *
     * @return \PDO
     */
    public function getConnection(): \PDO {
        return $this->connection;
    }
}