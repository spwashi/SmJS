<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:36 PM
 */

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sm\Core\Context\ResolutionContext;
use Sm\Core\Internal\Logging\LoggerFactory;
use Sm\Core\Paths\PathContainer;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\System_\Sm;

$resolutionContext = new ResolutionContext(PathContainer::init());
Sm::setResolutionContext($resolutionContext);

# Set up Logging (basic)
$fn_get_default_logger = function ($name = null, $severity = null) {
    if (isset($name)) {
        $name = StringResolvable::init($name)->resolve();
    }
    $Logger  = new Logger($name??'System');
    $Handler = new StreamHandler(SYSTEM_LOG_PATH . 'system.sm.log.html',
                                 $severity ?? null);
    $Handler->setFormatter(new HtmlFormatter);
    $Logger->pushHandler($Handler);
    return $Logger;
};
/** @var LoggerFactory $LoggerFactory */
//$LoggerFactory = Sm::resolveFactory(LoggerFactory::class);
//$LoggerFactory->register('System', OnceRunResolvable::init($fn_get_default_logger));