<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Util;
use Sm\Query\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Storage\Database\Table\TableSourceSchema;

/**
 * Class ColumnFormattingProxy
 *
 * Class that is going to help tell us stuff about an item in the context of being a column
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy
 */
class ColumnSchema_ColumnIdentifierFormattingProxy extends ColumnIdentifierFormattingProxy {
    /** @var  TableFormattingProxy $table */
    protected $table;
    protected $column_name;
    /** @var  \Sm\Query\Modules\Sql\Data\Column\ColumnSchema $subject */
    protected $subject;
    /**
     * ColumnSchema_ColumnIdentifierFormattingProxy constructor.
     *
     * @param                           $subject
     * @param SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!($subject instanceof ColumnSchema)) {
            throw new InvalidArgumentException("Wrong Formatting Proxy for type [" . Util::getShapeOfItem($subject) . ']');
        }
        parent::__construct($subject, $formattingProxyFactory);
    }
    /**
     * @return null|\Sm\Storage\Database\Table\TableSourceSchema
     */
    public function getTable(): ?TableSourceSchema {
        if (isset($this->table)) return $this->table;
        $tableSchema = $this->subject->getTableSchema();
        if (!$tableSchema) return null;
        return $this->table = $tableSchema;
    }
    public function getColumnName(): ?string {
        if (isset($this->column_name)) return $this->column_name;
        return $this->column_name = $this->subject->getName();
    }
}