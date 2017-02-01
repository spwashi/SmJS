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

class Module implements \Sm\Abstraction\Module\Module {
    /** @var Resolvable */
    protected $Dispatch = null;
    /** @var Resolvable */
    protected $Init = null;
    /** @var bool $is_init */
    protected $is_init = false;
    
    protected function __construct() { }
    public function dispatch(App $app = null, $arguments = null) {
        $arguments = Arguments::coerce($arguments);
        if (!$this->is_init) $this->initialize($app);
        if (isset($this->Dispatch)) {
            return $this->Dispatch->resolve($app, $arguments);
        } else {
            throw new UnresolvableError("Cannot resolve module");
        }
    }
    public function reset() {
        $this->is_init = false;
    }
    public function setDispatch(Resolvable $resolvable) {
        $this->Dispatch = $resolvable;
        return $this;
    }
    public function initialize(App $app = null) {
        if ($this->is_init) return $this;
        if ($this->Init instanceof Resolvable) $this->Init->resolve($app);
        $this->is_init = true;
        return $this;
    }
    /**
     * @param null             $item
     * @param \Sm\App\App|null $app
     *
     * @return static
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public static function init($item = null, App $app = null) {
        if ($item instanceof Module) return $item;
        $init = null;
        if (is_array($item)) {
            $init = $item['init'] ?? null;
            $item = $item['dispatch'] ?? null;
        }
        if (class_exists('\Sm\Resolvable\ResolvableFactory')) {
            try {
                $ResolvableFactory = isset($app) ? $app->resolve('ResolvableFactory') : null;
            } catch (UnresolvableError $e) {
                $ResolvableFactory = null;
            }
            $item = \Sm\Resolvable\ResolvableFactory::coerce($ResolvableFactory)->build($item);
            $init = \Sm\Resolvable\ResolvableFactory::coerce($ResolvableFactory)->build($init);
        }
        if (!($item instanceof Resolvable)) {
            throw new UnresolvableError("Cannot resolve module");
        }
        $Module = new static;
        if ($init instanceof Resolvable) {
            $Module->Init = $init;
        }
        $Module->setDispatch($item);
        $Module->initialize($app);
        return $Module;
    }
    /**
     * @param          $item
     * @param App|null $app
     *
     * @return Module
     * @throws UnresolvableError
     */
    public static function coerce($item, App $app = null) {
        return static::init($item, $app);
    }
}