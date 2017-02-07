<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:11 PM
 */

namespace Sm\App\Module;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\App\App;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Resolvable\ResolvableFactory;

class Module extends \Sm\Resolvable\Resolvable implements \Sm\Abstraction\Module\Module {
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
        $arguments = Arguments::coerce(func_get_args());
        if (!$this->is_init) $this->initialize();
    
        if ($this->has_dispatched) return $this->last_dispatch_result;
        
        if (isset($this->Dispatch)) {
            $this->has_dispatched = true;
            return $this->last_dispatch_result = $this->Dispatch->resolve($this->App, ...$arguments->_list());
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
     * @param \Sm\App\App $app
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
        if ($this->is_init) return $this;
        if ($this->Init instanceof Resolvable) {
            $this->Init->resolve($this->App ?? $App ?? null);
        }
        $this->is_init = true;
        return $this;
    }
    public function reset() {
        $this->is_init        = false;
        $this->has_dispatched = false;
        return $this;
    }
    public function resolve() {
        $this->initialize();
        return $this;
    }
    /**
     * @param null $item
     *
     * @return static
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public static function init($item = null) {
        if ($item instanceof Module) return $item;
        $init = null;
        if (is_array($item)) {
            $init = $item['init'] ?? null;
            $item = $item['dispatch'] ?? null;
        }
        if (class_exists('\Sm\Resolvable\ResolvableFactory')) {
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
}