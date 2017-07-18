<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 9:34 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Database;


use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormattingProxyFactory;

/**
 * Class String_DatabaseFormattingProxy
 *
 * FormattingProxies for Databases
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\
 */
class String_DatabaseFormattingProxy extends DatabaseFormattingProxy {
    /** @var  string */
    protected $database_name;
    
    public function __construct($subject, FormattingProxyFactory $formattingProxyFactory) {
        if (!is_string($subject)) throw new UnimplementedError("+ Formatting things that aren't strings");
        parent::__construct($subject, $formattingProxyFactory);
    }
    
    public function getDatabaseName(): string {
        if (isset($this->database_name)) return $this->database_name;
        return $this->database_name = $this->subject;
    }
}