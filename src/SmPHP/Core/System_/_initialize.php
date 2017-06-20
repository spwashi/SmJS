<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:36 PM
 */

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sm\Communication\Response\View\Template\TemplateFactory;
use Sm\Communication\Response\View\ViewFactory;
use Sm\Core\Internal\Logging\LoggerFactory;
use Sm\Core\Resolvable\OnceRunResolvable;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\System_\System_;


# todo Test this for integration
#  because this is more of an integration-based thing, I didn't test it here.
#   In the future, please please do


System_::registerFactory(ResolvableFactory::class, new ResolvableFactory);
System_::registerFactory(TemplateFactory::class, new TemplateFactory);
System_::registerFactory(ViewFactory::class, new ViewFactory);
System_::registerFactory(LoggerFactory::class, new LoggerFactory);

$LoggerFactory = System_::Factory(LoggerFactory::class);

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


$LoggerFactory->register('System', OnceRunResolvable::init($fn_get_default_logger));