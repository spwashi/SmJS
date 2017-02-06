<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 4:51 PM
 */

namespace Sm\App;


use Sm\App\Module\Module;
use Sm\IoC\IoC;
use Sm\Request\Request;

/**
 * @property Module routing
 * @property Module $_app
 * @property Module test
 *
 * @method   mixed routing(Request $request)
 */
class ModuleContainer extends IoC {
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
     * @param \Sm\App\App $App
     *
     * @return $this|static
     */
    public function duplicate(App $App = null) {
        $IoC = static::init();
        if (isset($App)) $IoC->setApp($App);
        $registry                  = $this->cloneRegistry();
        $IoC->_registered_defaults = $this->_registered_defaults;
        $IoC->register($registry, null, false);
        $IoC->reset();
        return $IoC;
    }
    /**
     * @param array|null|string|null                                         $name
     * @param callable|mixed|null|\Sm\Abstraction\Resolvable\Resolvable|null $registrand
     *
     * @param bool                                                           $do_initialize
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
            $registrand = Module::coerce($registrand);
            $registrand->setApp($this->App);
            
            if ($do_initialize) $registrand->initialize();
            
            parent::register($name, $registrand);
            return $this;
        }
    }
    /**
     * @param null|string|null $name
     * @param mixed|null|null  $arguments
     *
     * @return Module
     */
    public function resolve($name = null, $arguments = null) {
        $Module = parent::resolve($name, $arguments);
        if ($Module instanceof Module) $Module->initialize();
        return $Module;
    }
    /**
     * Reset all modules contained in this Container
     *
     * @return $this
     */
    public function reset() {
        foreach ($this->registry as $key => $value) {
            if ($value instanceof Module) $value->reset();
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