<?php
/**
 * User: Sam Washington
 * Date: 1/27/17
 * Time: 2:11 PM
 */

namespace Sm\App\Module;


use Sm\Abstraction\Resolvable\Resolvable;
use Sm\App\App;
use Sm\Resolvable\Error\UnresolvableError;

class Module implements \Sm\Abstraction\Module\Module {
    /**
     * @var Resolvable
     */
    protected $DispatchResolvable = null;
    public static function init() {
        return new static;
    }
    /**
     * @param          $item
     * @param App|null $app
     *
     * @return Module
     * @throws UnresolvableError
     */
    public static function coerce($item, App $app = null) {
        if ($item instanceof \Sm\Abstraction\Module\Module) return $item;
        if (is_array($item)) {
            $item = $item['dispatch'] ?? null;
        }
        if (class_exists('\Sm\Resolvable\ResolvableFactory')) {
            try {
                $ResolvableFactory = isset($app) ? $app->resolve('ResolvableFactory') : null;
            } catch (UnresolvableError $e) {
                $ResolvableFactory = null;
            }
            $item = \Sm\Resolvable\ResolvableFactory::coerce($ResolvableFactory)->build($item);
        }
        if (!($item instanceof Resolvable)) {
            throw new UnresolvableError("Cannot dispatch module");
        }
        return static::init()->setDispatch($item);
    }
    public function dispatch(App $app = null) {
        if (isset($this->DispatchResolvable)) {
            $this->DispatchResolvable->resolve($app);
        } else {
            throw new UnresolvableError("Cannot dispatch module");
        }
    }
    public function setDispatch(Resolvable $resolvable) {
        $this->DispatchResolvable = $resolvable;
        return $this;
    }
}