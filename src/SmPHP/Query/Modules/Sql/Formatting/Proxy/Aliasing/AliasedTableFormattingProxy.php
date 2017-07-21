<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 8:45 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing;


use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\Proxy\Database\DatabaseFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * Class TableAliasProxy
 *
 * Represents an Aliased Table
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing
 * @method  AliasedTableFormattingProxy static init(...$items)
 */
class AliasedTableFormattingProxy extends SqlFormattingProxy implements TableFormattingProxy, AliasedFormattingProxy {
    protected $alias;
    /** @var  \Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableNameFormattingProxy */
    protected $subject;
    /**
     * AliasedTableFormattingProxy constructor.
     *
     * @param                                                            $subject
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!($subject instanceof TableFormattingProxy)) throw new UnimplementedError("Can only alias TableFormattingProxies");
        parent::__construct($subject, $formattingProxyFactory);
    }
    
    public function getName(): string {
        return $this->alias;
    }
    public function getUnaliasedTableName() {
        return $this->subject->getName();
    }
    public function getDatabase(): ?DatabaseFormattingProxy {
        return $this->subject->getDatabase();
    }
    /**
     * Set the Alias of the FormattingProxy
     *
     * @param $alias
     *
     * @return $this
     */
    public function setAlias(string $alias) {
        $this->alias = $alias;
        return $this;
    }
}