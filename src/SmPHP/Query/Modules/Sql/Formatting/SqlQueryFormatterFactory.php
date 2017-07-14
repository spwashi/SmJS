<?php
/**
 * User: Sam Washington
 * Date: 7/9/17
 * Time: 6:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Formatting\Formatter\FormatterFactory;

/**
 * Class SqlQueryFormatterFactory
 *
 * @package Sm\Query\Modules\Sql\Formatting
 * @method Formatter resolve($name = null)
 */
class SqlQueryFormatterFactory extends FormatterFactory {
    protected $formattingProxyFactory;
    /**
     * SqlQueryFormatterFactory constructor.
     *
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory $formattingProxyFactory
     */
    public function __construct(SqlFormattingProxyFactory $formattingProxyFactory) {
        $this->formattingProxyFactory = $formattingProxyFactory;
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
}