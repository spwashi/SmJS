<?php
/**
 * User: Sam Washington
 * Date: 7/8/17
 * Time: 5:54 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Query\Modules\Sql\Formatting\Aliasing\SqlFormattingAliasContainer;

/**
 * Class SqlQueryFormatter
 *
 * Given a Statement, Clause, or whatever we call Queries,
 * return a string representation of the Query
 *
 * @package Sm\Query\Modules\Sql
 */
class SqlQueryFormatter implements Formatter {
    /** @var  \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory $formatterFactory */
    protected $formatterFactory;
    /**
     * SqlQueryFormatter constructor.
     *
     * @param \Sm\Query\Modules\Sql\Formatting\SqlQueryFormatterFactory $formatterFactory The Factory that tells us how things get formatted
     * @param  Aliasing\SqlFormattingAliasContainer                     $aliasContainer   A Container that will tell us how we should change something
     */
    public function __construct(SqlQueryFormatterFactory $formatterFactory) {
        $this->formatterFactory = $formatterFactory;
    }
    /**
     * Return the item Formatted in the specific way
     *
     * @param $statement
     *
     * @return mixed
     * @throws \Sm\Core\Exception\InvalidArgumentException
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function format($statement): string {
        return $this->formatterFactory->format($statement);
    }
    public function proxy($item, $as) {
        return $this->formatterFactory->proxy($item, $as);
    }
    /**
     * @return Aliasing\SqlFormattingAliasContainer
     */
    protected function getAliasContainer(): SqlFormattingAliasContainer {
        return $this->formatterFactory->getAliasContainer();
    }
}