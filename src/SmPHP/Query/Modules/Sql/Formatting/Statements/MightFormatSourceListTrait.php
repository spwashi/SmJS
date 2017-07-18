<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 6:01 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Statements;


use Sm\Core\Formatting\Formatter\Exception\IncompleteFormatterException;
use Sm\Query\Modules\Sql\Formatting\Proxy\Table\TableIdentifierFormattingProxy;

/**
 * Class MightFormatSourceListTrait
 *
 * Trait for (Statements?) that might ned to use a list of Sources
 *
 * @package Sm\Query\Modules\Sql\Formatting\Statements
 */
trait MightFormatSourceListTrait {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory */
    protected $formatterFactory;
    protected function formatSourceList($source_array): string {
        $sources = [];
        if (!isset($this->formatterFactory)) throw new IncompleteFormatterException();
        foreach ($source_array as $index => $source) {
            $formatter = $this->formatterFactory;
            $source    = $this->formatterFactory->format($formatter->proxy($source,
                                                                           TableIdentifierFormattingProxy::class));
            $sources[] = $source;
        }
        return join(', ', $sources);
    }
}