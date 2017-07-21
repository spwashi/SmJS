<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 8:36 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormattingProxyFactory;
use Sm\Query\Modules\Sql\Formatting\Proxy\Database\DatabaseFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Database\String_DatabaseFormattingProxy;

/**
 * Class TableFormattingProxy
 *
 * Formats tables
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
class String_TableReferenceFormattingProxy extends TableReferenceFormattingProxy {
    /**
     * TableFormattingProxy constructor.
     *
     * @param                                                      $subject
     * @param \Sm\Core\Formatting\Formatter\FormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function __construct($subject, FormattingProxyFactory $formattingProxyFactory) {
        if (!is_string($subject)) throw new UnimplementedError("+ Formatting things that aren't strings");
        parent::__construct($subject, $formattingProxyFactory);
    }
    /**
     * Get a formatting proxy representing the Database that this will belong to
     *
     * @return null|\Sm\Query\Modules\Sql\Formatting\Proxy\Database\DatabaseFormattingProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function getDatabase():? DatabaseFormattingProxy {
        if ($this->database) return $this->database;
        
        $database_name = null;
        
        if (strpos($this->subject, '.') === false) return null;
        
        $expl = explode('.', $this->subject);
        # database.table_name
        if (count($expl) === 2) $database_name = $expl[0];
        else throw new InvalidArgumentException("Cannot format subjects of 'xxx.database.table_name*' format");
    
        $this->database = $this->getFormattingProxyFactory()->build(String_DatabaseFormattingProxy::class, $database_name);
        
        return $this->database;
    }
    /**
     * Get the name of the Table
     *
     * @return string
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function getName(): string {
        if (isset($this->table_name)) return $this->table_name;
        
        # If if is a string like (db.table_name) or somethin
        if (strpos($this->subject, '.')) {
            $expl = explode('.', $this->subject);
            # database.table_name
            if (count($expl) === 2) return $this->table_name = $expl[1];
            
            throw new InvalidArgumentException("Cannot format subjects of 'xxx.database.table_name*' format");
        }
        
        # Otherwise assume the table name doesn't match
        return $this->table_name = $this->subject;
    }
}