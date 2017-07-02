<?php
/**
 * User: Sam Washington
 * Date: 6/23/17
 * Time: 2:25 PM
 */

namespace Sm\Core\Context\Layer;


use Sm\Core\Context\AbstractContext;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Module\Error\InvalidModuleException;
use Sm\Core\Module\Module;
use Sm\Core\Module\ModuleContainer;

/**
 * Class Layer
 *
 * A layer represents a set of Modules and core classes that work together to fulfill one
 * particular realm of functionality. They are meant to more explicitly structure code
 * around modularity and loose coupling.
 *
 * Each Layer only exposes Modules that other layers can use to interact with them.
 * The goal is to discourage code that is too powerful.
 *
 * @package Sm\Core\Context
 */
abstract class Layer extends AbstractContext {
    /** @var \Sm\Core\Module\ModuleContainer $ModuleContainer */
    protected $ModuleContainer;
    /** @var array An array of the Layer Roots we checked applicability for */
    protected $checked_layer_root_ids = [];
    
    use HasObjectIdentityTrait;
    
    public function __construct(ModuleContainer $moduleContainer = null) {
        $this->ModuleContainer = $moduleContainer ?? new ModuleContainer;
        parent::__construct();
    }
    /**
     * Check to see if this Layer is operational on this LayerRoot. Throw an exception otherwise
     *
     * @param \Sm\Core\Context\Layer\LayerRoot $layerRoot
     *
     * @return bool|null True if it's good, null if it's been done, throws error otherwise
     */
    public function check(LayerRoot $layerRoot):?bool {
        # Null if we've already authorized this LayerRoot
        if (in_array($layerRoot->getObjectId(), $this->checked_layer_root_ids)) return null;
        
        # Otherwise this is okay
        return true;
    }
    /**
     * Initialize the Layer on the LayerRoot provided
     *
     * @param \Sm\Core\Context\Layer\LayerRoot $layerRoot
     *
     * @return mixed
     */
    public function initialize(LayerRoot $layerRoot): LayerProxy {
        $this->check($layerRoot);
        //todo do more on initialization... THINK!
        return new LayerProxy($this, $layerRoot);
    }
    
    
    /**
     * Register a Module under this Layer
     *
     * @param string                                                $name   The name that this Module will take under this Layer
     * @param \Sm\Core\Module\AbstractModule|\Sm\Core\Module\Module $module The Module that we are registering under this Layer
     *
     * @throws \Sm\Core\Module\Error\InvalidModuleException If we try to register a Module that we actually can't
     */
    public function registerModule(string $name, Module $module) {
        $expected_modules = $this->_listExpectedModules();
        if (!in_array($name, $expected_modules)) {
            $st_class = static::class;
            throw new InvalidModuleException("Cannot register module {$name} on layer {$st_class}");
        }
        $module->initialize($this);
        $this->ModuleContainer->register($name, $module);
    }
    
    /**
     * @return array An array of strings corresponding to the names of the modules that this layer needs to have
     */
    abstract protected function _listExpectedModules(): array;
}