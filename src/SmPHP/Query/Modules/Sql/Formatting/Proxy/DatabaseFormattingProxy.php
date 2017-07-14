<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 9:34 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormattingProxyFactory;

/**
 * Class DatabaseFormattingProxy
 *
 * FormattingProxies for Databases
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\
 */
class DatabaseFormattingProxy extends SqlFormattingProxy {
    public function __construct($subject, FormattingProxyFactory $formattingProxyFactory) {
        if (!is_string($subject)) throw new UnimplementedError("+ Formatting things that aren't strings");
        parent::__construct($subject, $formattingProxyFactory);
    }
}