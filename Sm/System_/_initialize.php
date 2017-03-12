<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:36 PM
 */

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sm\Entity\EntityFactory;
use Sm\Logger\LoggerFactory;
use Sm\Resolvable\ResolvableFactory;
use Sm\Resolvable\SingletonFunctionResolvable;
use Sm\Resolvable\StringResolvable;
use Sm\System_\System_;
use Sm\View\Template\TemplateFactory;
use Sm\View\ViewFactory;


# todo Test this for integration
#  because this is more of an integration-based thing, I didn't test it here.
#   In the future, please please do


System_::registerFactory(ResolvableFactory::class, new ResolvableFactory);
System_::registerFactory(TemplateFactory::class, new TemplateFactory);
System_::registerFactory(ViewFactory::class, new ViewFactory);
System_::registerFactory(EntityFactory::class, new EntityFactory);
System_::registerFactory(LoggerFactory::class, new LoggerFactory);

$LoggerFactory = System_::Factory(LoggerFactory::class);

$fn_get_default_logger = function ($name = null, $severity = null) {
    if (isset($name)) $name = StringResolvable::coerce($name)->resolve();
    $Logger  = new Logger($name??'System');
    $Handler = new StreamHandler(SYSTEM_LOG_PATH . 'system.sm.log.html',
                                 $severity ?? null);
    $Handler->setFormatter(new HtmlFormatter);
    $Logger->pushHandler($Handler);
    return $Logger;
};


$LoggerFactory->register(SingletonFunctionResolvable::init($fn_get_default_logger),
                         'System');