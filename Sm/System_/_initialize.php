<?php
/**
 * User: Sam Washington
 * Date: 2/11/17
 * Time: 6:36 PM
 */

use Sm\Entity\EntityFactory;
use Sm\Resolvable\ResolvableFactory;
use Sm\System_\System_;
use Sm\View\Template\TemplateFactory;
use Sm\View\ViewFactory;

System_::registerFactory(ResolvableFactory::class, new ResolvableFactory);
System_::registerFactory(TemplateFactory::class, new TemplateFactory);
System_::registerFactory(ViewFactory::class, new ViewFactory);
System_::registerFactory(EntityFactory::class, new EntityFactory);