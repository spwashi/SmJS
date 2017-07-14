<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Factory\Factory;
use Sm\Core\Formatting\FormattingProxy;

/**
 * Class ColumnFormattingProxy
 *
 * Class that is going to help tell us stuff about an item in the context of being a column
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
class ColumnFormattingProxy implements FormattingProxy {
    protected $subject;
    #
    protected $column_name;
    protected $table;
    protected $type;
    protected $length;
    protected $can_be_null;
    protected $default;
    #
    /** @var \Sm\Core\Factory\Factory How we know how to build other Proxies */
    private $proxyFactory;
    #
    protected function __construct($column, Factory $proxyFactory) {
        if (is_string($column)) $this->subject = $column;
        else throw new UnimplementedError("+ format anything but a string as a column");
        
        $this->column_name  = $this->figureColumnName();
        $this->proxyFactory = $proxyFactory;
    }
    public function figureColumnTable() {
        if (isset($this->table)) return $this->table;
        
    }
    /**
     * Returns the assumed name of the column based on everything we know
     *
     * @return null|string
     */
    public function figureColumnName():?string {
        if (isset($this->column_name)) return $this->column_name;
        # If we are doing something separated by column name
        if (strpos($this->subject, '.')) {
            $explode = explode('.', $this->subject);
            return end($explode);
        }
        
        #todo check to see if the column name is malformed?
        
        return $this->subject;
    }
}