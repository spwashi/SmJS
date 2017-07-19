<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;

/**
 * Class SqlQueryFormatterFactory
 *
 * @package Sm\Query\Modules\Sql\Formatting
 * @method Formatter resolve($name = null)
 */
class SqlQueryFormatterFactory extends FormatterFactory {
    protected $formattingProxyFactory;
    /**
     * @var \Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer
     */
    private $aliasContainer;
    /**
     * SqlQueryFormatterFactory constructor.
     *
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory            $formattingProxyFactory
     * @param \Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer $aliasContainer
     */
    public function __construct(SqlFormattingProxyFactory $formattingProxyFactory, SqlFormattingAliasContainer $aliasContainer) {
        $this->formattingProxyFactory = $formattingProxyFactory;
        $this->aliasContainer         = $aliasContainer;
        parent::__construct();
    }
    /**
     * Return an item Proxied in a certain way
     *
     * @param mixed       $item
     * @param string|null $as
     *
     * @return mixed|null
     */
    public function proxy($item, string $as = null) {
        return isset($as)
            ? $this->formattingProxyFactory->build($as, $item, $this->formattingProxyFactory)
            : $this->formattingProxyFactory->build($item, $this->formattingProxyFactory);
    }
    
    /**
     * Get the object that will hold all of the Aliases for the FormatterFactory.
     *
     * #todo ideally this would only hold the aliases that are going to be used across the operation.
     *
     * @return \Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer
     */
    public function getAliasContainer(): SqlFormattingAliasContainer {
        return $this->aliasContainer;
    }
}