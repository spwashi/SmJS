<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 4:51 PM
 */

namespace Sm\Core\Application;


use Sm\Communication\Request\Request;
use Sm\Core\Application\Module\StandardModule;
use Sm\Core\Container\Container;

/**
 * @property StandardModule                            routing
 * @property StandardModule                            $_app
 * @property StandardModule                            test
 * @property \Sm\Storage\Modules\Sql\SqlStandardModule sql
 *
 * @method   mixed routing(Request $request)
 */
class ModuleContainer extends Container {
    public $App = null;
    /**
     * @return null
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param null $App
     *
     * @return ModuleContainer
     */
    public function setApp(App $App) {
        $this->App = $App;
        return $this;
    }
    /**
     * Return a new instance of this class that inherits this registry
     *
     * @param \Sm\Core\Application\App $App
     *
     * @return $this|static
     */
    public function duplicate(App $App = null) {
        $Container = static::init();
        if (isset($App)) {
            $Container->setApp($App);
        }
        $registry                        = $this->cloneRegistry();
        $Container->_registered_defaults = $this->_registered_defaults;
        $Container->register($registry, null, false);
        $Container->reset();
        return $Container;
    }
    /**
     * @param array|null|string|null                                              $name
     * @param callable|mixed|null|\Sm\Core\Abstraction\Resolvable\Resolvable|null $registrand
     *
     * @param bool                                                                $do_initialize
     *
     * @return $this
     */
    public function register($name = null, $registrand = null, $do_initialize = true) {
        if (is_array($name)) {
            foreach ($name as $index => $value) {
                $this->register($index, $value);
            }
            return $this;
        } else {
            $registrand = StandardModule::coerce($registrand);
            $registrand->setApp($this->App);
    
            if ($do_initialize) {
                $registrand->initialize();
            }
            
            parent::register($name, $registrand);
            return $this;
        }
    }
    /**
     * @param null|string|null $name
     * @param mixed|null|null  $arguments
     *
     * @return StandardModule
     */
    public function resolve($name = null, $arguments = null) {
        $Module = parent::resolve($name, $arguments);
        if ($Module instanceof StandardModule) {
            $Module->initialize();
        }
        return $Module;
    }
    /**
     * Reset all modules contained in this Container
     *
     * @return $this
     */
    public function reset() {
        foreach ($this->getAll() as $key => $value) {
            if ($value instanceof StandardModule) {
                $value->reset();
            }
        }
        return $this;
    }
    function __call($name, $arguments) {
        if ($this->canResolve($name)) {
            $module = $this->resolve($name);
            return $module->dispatch(...$arguments);
        }
        return null;
    }
    
}