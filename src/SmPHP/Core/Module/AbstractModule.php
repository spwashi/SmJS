<?php
/**
 * User: Sam Washington
 * Date: 6/21/17
 * Time: 11:27 AM
 */

namespace Sm\Core\Module;


use Sm\Core\Context\Context;
use Sm\Core\Hook\HasHooksTrait;
use Sm\Core\Hook\Hook;
use Sm\Core\Hook\HookHaver;

/**
 * Class Module
 *
 * Represents something that we plug in to the framework or application in order to modify the functionality
 * of it a bit more dynamically.
 *
 * One key feature of Modules are the Hooks that tie into them --
 * -- these allow us to modify a Module's functionality essentially on the fly
 *
 * Is this a good thing? Maybe, maybe not. Hopefully hooks and Modules are used responsibly.
 *
 * @package Sm\Core\Module
 */
abstract class AbstractModule implements HookHaver, Module {
    use HasHooksTrait;
    
    /** @var array An array of the object_ids of the Contexts this Module has access to. */
    protected $verified_contexts = [];
    
    /**
     * Get the Module 'primed' in whatever Context we call it.
     * This might mean registering classes or other things.
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return null|\Sm\Core\Module\ModuleProxy
     */
    public final function initialize(Context $context): ?ModuleProxy {
        # Check to see if we can initialize the Module within this context
        $this->check($context);
        $this->resolveHook(Hook::INIT, $context);
        $this->_initialize($context);
        # Return a ModuleProxy that will allow us to refer to this Module within this Context consistently
        return $this->createModuleProxy($context);
    }
    /**
     * Throw an error if the Context is not valid for whatever reason
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Exception\Exception
     * @return bool|null
     */
    public final function check(Context $context):?bool {
        if ($this->hasValidatedContext($context)) return null;
        
        # This should throw an error if the Module is not applicable on this context
        $this->resolveHook(Hook::CHECK, $context);
        $this->_check($context);
        
        $this->addValidatedContext($context);
        return true;
    }
    /**
     * Do the necessary things to remove this Module from the Context it was applied to
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @throws \Sm\Core\Exception\Exception
     * @return bool|null true if successful, null if the context hasn't been validated to begin with, error otherwise
     */
    public final function deactivate(Context $context) {
        if (!$this->hasValidatedContext($context)) return null;
        $this->check($context);
        $this->resolveHook(Hook::DEACTIVATE, $context);
        $this->_deactivate($context);
        $index = array_search($context->getObjectId(), $this->verified_contexts);
        unset($this->verified_contexts[ $index ]);
        return true;
    }
    
    /**
     * @param \Sm\Core\Context\Context $context
     *
     * @return bool
     */
    protected function hasValidatedContext(Context $context): bool {
        return in_array($context->getObjectId(), $this->verified_contexts);
    }
    /**
     * Add it to a list of Contexts we've verified so we know that it's okay.
     * subclasses might want to add some sort of exipiry functionality
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return mixed
     */
    protected function addValidatedContext(Context $context) {
        return $this->verified_contexts[] = $context->getObjectId();
    }
    
    /**
     * Set up the Class. Meant to be overridden
     *
     * @see \Sm\Core\Module\AbstractModule::initialize
     *
     * @param \Sm\Core\Context\Context $context
     */
    protected function _initialize(Context $context) { }
    /**
     * Set up the Class. Meant to be overridden
     *
     * @see \Sm\Core\Module\AbstractModule::deactivate
     *
     * @param \Sm\Core\Context\Context $context
     */
    protected function _deactivate(Context $context) { }
    /**
     * Check to see if the Module is applicable in this context. Meant to be overridden
     *
     * @see \Sm\Core\Module\AbstractModule::check
     *
     * @param \Sm\Core\Context\Context $context
     */
    protected function _check(Context $context) { }
    
    /**
     * Return a ModuleProxy that maps on to this Module within a given Context
     *
     * @param \Sm\Core\Context\Context $context
     *
     * @return \Sm\Core\Module\ModuleProxy
     */
    protected function createModuleProxy(Context $context): ModuleProxy {
        return new ModuleProxy($this, $context);
    }
}