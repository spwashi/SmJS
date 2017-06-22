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
use Sm\Core\Factory\FactoryContainer;
use Sm\Core\Internal\Logging\LoggerFactory;
use Sm\Core\Resolvable\OnceRunResolvable;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\System_\Sm;
use Sm\Presentation\View\Template\TemplateFactory;
use Sm\Presentation\View\ViewFactory;

$resolutionContext = new ResolutionContext;
$resolutionContext->setFactoryContainer(new FactoryContainer([
                                                                 ResolvableFactory::class => new ResolvableFactory,
                                                                 TemplateFactory::class   => new TemplateFactory,
                                                                 ViewFactory::class       => new ViewFactory,
                                                                 LoggerFactory::class     => new LoggerFactory,
                                                             ]));
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
$LoggerFactory = Sm::resolveFactory(LoggerFactory::class);
$LoggerFactory->register('System', OnceRunResolvable::init($fn_get_default_logger));