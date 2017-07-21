<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Core\Util;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;
use Sm\Query\Modules\Sql\Formatting\Proxy\PlaceholderFormattingProxy;

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
     * @var \Sm\Query\Modules\Sql\Formatting\SqlFormattingContext
     */
    private $context;
    
    /**
     * SqlQueryFormatterFactory constructor.
     *
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory            $formattingProxyFactory
     * @param \Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer $aliasContainer
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingContext                 $context
     */
    public function __construct(SqlFormattingProxyFactory $formattingProxyFactory,
                                SqlFormattingAliasContainer $aliasContainer,
                                SqlFormattingContext $context) {
        $this->formattingProxyFactory = $formattingProxyFactory;
        $this->aliasContainer         = $aliasContainer;
        $this->context                = $context;
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
        if (isset($as)) {
            return $this->formattingProxyFactory->build($as, $item, $this->formattingProxyFactory);
        } else {
            return $this->formattingProxyFactory->build($item, $this->formattingProxyFactory);
        }
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
    public function getContext(): SqlFormattingContext { return $this->context; }
    public function placeholder($value, $name = null) {
        $name = $name??Util::generateRandomString(4, Util::getAlphaCharacters(0));
        $this->context->addVariables([ $name => $value ]);
        return $this->proxy([ $name, $value, ], PlaceholderFormattingProxy::class);
    }
}