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

class Module extends \Sm\Resolvable\Resolvable implements \Sm\Abstraction\Module\Module {
    /** @var Resolvable */
    protected $Dispatch = null;
    /** @var Resolvable */
    protected $Init = null;
    /** @var bool $is_init */
    protected $is_init = false;
    protected $App;
    
    public function dispatch($arguments = null) {
        $arguments = Arguments::coerce($arguments);
        if (!$this->is_init) $this->initialize();
        if (isset($this->Dispatch)) {
            return $this->Dispatch->resolve($this->App??null, $arguments);
        } else {
            throw new UnresolvableError("Cannot resolve module");
        }
    }
    /**
     * @return mixed
     */
    public function getApp() {
        return $this->App;
    }
    public function setApp(App $app) {
        $this->App = $app;
        return $this;
    }
    public function setDispatch(Resolvable $resolvable) {
        $this->Dispatch = $resolvable;
        return $this;
    }
    public function initialize() {
        if ($this->is_init) return $this;
        if ($this->Init instanceof Resolvable) {
            $this->Init->resolve($this->App ?? null);
            $this->is_init = true;
        }
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
            $item = \Sm\Resolvable\ResolvableFactory::init()->build($item);
            $init = \Sm\Resolvable\ResolvableFactory::init()->build($init);
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
    public function reset() {
        $this->is_init = false;
        return $this;
    }
    /**
     * @param Arguments|null|mixed $_ ,..
     *
     * @return mixed
     */
    public function resolve() {
        $this->initialize();
        return $this;
    }
}