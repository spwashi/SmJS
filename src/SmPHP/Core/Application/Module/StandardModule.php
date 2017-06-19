<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:11 PM
 */

namespace Sm\Core\Application\Module;


use Sm\Core\Abstraction\Resolvable\Resolvable;
use Sm\Core\Application\App;
use Sm\Core\Resolvable\Error\UnresolvableError;
use Sm\Core\Resolvable\ResolvableFactory;

class StandardModule extends \Sm\Core\Resolvable\Resolvable implements Module {
    /** @var Resolvable */
    protected $Dispatch = null;
    /** @var Resolvable */
    protected $Init = null;
    /** @var bool $is_init */
    protected $is_init              = false;
    protected $has_dispatched       = false;
    protected $last_dispatch_result = null;
    protected $App;
    
    public function dispatch() {
        $arguments = func_get_args();
        if (!$this->is_init) {
            $this->initialize();
        }
        $this->assertComplete();
    
        if ($this->has_dispatched) {
            return $this->last_dispatch_result;
        }
        
        if (isset($this->Dispatch)) {
            $this->has_dispatched = true;
            return $this->last_dispatch_result = $this->Dispatch->resolve($this->App, ...$arguments);
        } else {
            throw new UnresolvableError("Cannot resolve module");
        }
    }
    /**
     * @return App
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param \Sm\Core\Application\App $app
     *
     * @return $this
     */
    public function setApp(App $app) {
        $this->App = $app;
        return $this;
    }
    public function setDispatch(Resolvable $resolvable) {
        $this->Dispatch = $resolvable;
        return $this;
    }
    public function initialize(App $App = null) {
        if ($this->is_init) {
            return $this;
        }
        if ($this->Init instanceof Resolvable) {
            $this->Init->resolve($this->App ?? $App ?? null, $this);
        }
        $this->is_init = true;
        return $this;
    }
    public function reset() {
        $this->is_init        = false;
        $this->has_dispatched = false;
        return $this;
    }
    public function resolve($_ = null) {
        $this->initialize();
        return $this;
    }
    /**
     * @param null $item
     *
     * @return static
     * @throws \Sm\Core\Resolvable\Error\UnresolvableError
     */
    public static function init($item = null) {
        if ($item instanceof StandardModule) {
            return $item;
        }
        $init = null;
        if (is_array($item)) {
            $init = $item['init'] ?? null;
            $item = $item['dispatch'] ?? null;
        } else if (is_callable($item)) {
            $init = $item;
        }
        if (class_exists('\Sm\Core\Resolvable\ResolvableFactory')) {
            $item = ResolvableFactory::init()->build($item);
            $init = ResolvableFactory::init()->build($init);
        }
        if (!($item instanceof Resolvable)) {
            throw new UnresolvableError("Cannot resolve module");
        }
        $Module = new static;
        if ($init instanceof Resolvable) {
            $Module->Init = $init;
        }
        $Module->setDispatch($item);
        return $Module;
    }
    /**
     * Throw an error if this class is not complete.
     *
     * @throws \Sm\Core\Resolvable\Error\UnresolvableError
     * @return bool
     */
    protected function assertComplete() {
        return true;
    }
}