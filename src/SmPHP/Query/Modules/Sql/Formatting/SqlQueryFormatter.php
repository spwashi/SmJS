<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:54 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Util;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing\AliasedFormattingProxy;

/**
 * Class SqlQueryFormatter
 *
 * Given a Statement, Clause, or whatever we call Queries,
 * return a string representation of the Query
 *
 * @package Sm\Query\Modules\Sql
 */
class SqlQueryFormatter implements Formatter {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory $queryFormatter */
    protected $queryFormatter;
    /** @var  SqlFormattingAliasContainer $aliasContainer */
    protected $aliasContainer;
    /**
     * SqlQueryFormatter constructor.
     *
     * @param \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory $formatterFactory The Factory that tells us how things get formatted
     * @param  Aliasing\SqlFormattingAliasContainer                     $aliasContainer   A Container that will tell us how we should change something
     */
    public function __construct(SqlQueryFormatterFactory $formatterFactory, SqlFormattingAliasContainer $aliasContainer = null) {
        $this->queryFormatter = $formatterFactory;
        $this->aliasContainer = $aliasContainer ?? $this->queryFormatter->getAliasContainer();
    }
    /**
     * Return the item Formatted in the specific way
     *
     * @param $columnSchema
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($columnSchema): string {
        return $this->formatComponent($columnSchema);
    }
    /**
     * Create a Proxy so we can interact with a component of this Formatter's process as it would exist within a certain context
     *
     * @param $item
     * @param $as
     *
     * @return mixed|null
     */
    public function proxy($item, $as) {
        if ($proxy = $this->aliasContainer->resolveProxy($item)) return $proxy;
        
        $proxy = $this->queryFormatter->proxy($item, $as);
        $this->aliasContainer->registerProxy($item, $proxy);
        return $proxy;
    }
    /**
     * @return Aliasing\SqlFormattingAliasContainer
     */
    protected function getAliasContainer(): SqlFormattingAliasContainer {
        return $this->aliasContainer;
    }
    /**
     * Set the AliasContainer that will be used by this Formatter
     *
     * @param \Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer $aliasContainer
     *
     * @return $this
     */
    protected function setAliasContainer(SqlFormattingAliasContainer $aliasContainer) {
        $this->aliasContainer = $aliasContainer;
        return $this;
    }
    
    /**
     * Alias an item, using the classname provided as the AliasedFormattingProxy we will wrap the item around
     *
     * @param        $item
     * @param string $alias_classname MUST BE AN AliasedFormattingProxy classname. This is what we will use to hold the Alias
     * @param null   $alias_name
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    protected function alias($item, string $alias_classname, $alias_name = null) {
        if (!is_a($alias_classname, AliasedFormattingProxy::class, 1)) {
            throw new InvalidArgumentException("can only use AliasedFormattingProxies as aliases");
        }
        
        /** @var \Sm\Query\Modules\Sql\Formatting\Proxy\Aliasing\AliasedFormattingProxy $aliasProxy */
        $aliasProxy = $this->proxy($item, $alias_classname);
        
        # Creat an alias randomly if one was not specified
        if (!$alias_name) $alias_name = Util::generateRandomString(5, Util::ALPHA);
        
        $aliasProxy->setAlias($alias_name);
        
        $this->aliasContainer->register($item, $aliasProxy);
        
        return $this;
    }
    protected function getFinalAlias($item) {
        return $this->aliasContainer->getFinalAlias($item);
    }
    /**
     * Format something used by
     *
     * @param $component
     *
     * @return mixed
     */
    protected function formatComponent($component) {
        if (!isset($component)) return null;
        $formatter = $this->queryFormatter->build($component);
        if ($formatter instanceof SqlQueryFormatter) $formatter->setAliasContainer($this->getAliasContainer());
        return $formatter->format($component);
    }
}