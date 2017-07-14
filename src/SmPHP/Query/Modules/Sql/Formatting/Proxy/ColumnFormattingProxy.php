<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormattingProxyFactory;

/**
 * Class ColumnFormattingProxy
 *
 * Class that is going to help tell us stuff about an item in the context of being a column
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
class ColumnFormattingProxy extends SqlFormattingProxy {
    protected $subject;
    #
    protected $column_name;
    protected $table;
    protected $type;
    protected $length;
    protected $can_be_null;
    protected $default;
    #
    public function __construct($column, FormattingProxyFactory $formattingProxyFactory) {
        if (!is_string($column)) throw new UnimplementedError("+ format anything but a string as a column");
        parent::__construct($column, $formattingProxyFactory);
    }
    /**
     * @return \Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy|\Sm\Query\Modules\Sql\Formatting\Proxy\TableFormattingProxy
     */
    public function getTable(): ?TableFormattingProxy {
        if (isset($this->table)) return $this->table;
        
        
        if (strpos($this->subject, '.') === false) return null;
        
        $table_name = null;
        $explode    = explode('.', $this->subject);
        $count      = count($explode);
        if ($count === 2) {
            $table_name = $explode[0];
        } else if ($count === 3) {
            $table_name = $explode[1];
        } else {
            throw new InvalidArgumentException("Improper subject for table");
        }
        
        return $this->table = $this->getFormattingProxyFactory()->build(TableFormattingProxy::class, $table_name);
    }
    /**
     * Returns the assumed name of the column based on everything we know
     *
     * @return null|string
     */
    public function getColumnName():?string {
        if (isset($this->column_name)) return $this->column_name;
        # If we are doing something separated by column name
        if (strpos($this->subject, '.')) {
            $explode = explode('.', $this->subject);
            return $this->column_name = end($explode);
        }
        
        #todo check to see if the column name is malformed?
        
        return $this->column_name = $this->subject;
    }
}