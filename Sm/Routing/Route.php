<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 2:38 AM
 */

namespace Sm\Routing;


use Sm\Abstraction\Coercable;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Request\Request;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\ResolvableFactory;
use Sm\Resolvable\StringResolvable;

class Route implements Resolvable, Coercable, \JsonSerializable {
    /** @var  Resolvable $DefaultResolvable */
    protected $DefaultResolvable;
    /** @var  Resolvable $Resolution */
    protected $Resolution;
    protected $pattern;
    protected $parameters = [];
    
    public function __construct($resolution = null, $pattern = null) {
        if (is_string($pattern) || is_numeric($pattern)) {
            $this->setStringPattern($pattern);
        }
        
        if (is_string($resolution) && strpos($resolution, '#') !== false && strpos($resolution, '::') !== false) {
            $resolution = FunctionResolvable::coerce(function ($Request = null) use ($pattern, $resolution) {
                if ($Request instanceof Request) {
                    $App        = $Request->getApp();
                    $resolution = str_replace('#', $App ? $App->controller_namespace : '', $resolution);
                }
                $resolution_expl = explode('::', $resolution);
                $class_name      = $resolution_expl[0];
                $method_name     = $resolution_expl[1] ?? null;
    
                # If the class doesn't have the requested method, skip it
                if ((!$class_name || !$method_name) || !(class_exists($class_name) || !method_exists($class_name, $method_name))) {
                    throw new UnresolvableError("Malformed method- {$resolution}");
                }
    
                $resolution = [
                    new $class_name($App ?? null),
                    $method_name,
                ];
                return FunctionResolvable::coerce($resolution)->resolve(...func_get_args());
            });
        } else {
            $resolution = ResolvableFactory::init()->build($resolution);
        }
    
        if ($resolution instanceof Resolvable) {
            $this->setResolution($resolution);
        }
    }
    /**
     * @param string|Request $item
     *
     * @return bool
     */
    public function matches($item) {
        if ($item instanceof Request) {
            $item = $item->getUrlPath();
        }
        if (is_string($item)) {
            $item = trim($item, '\\ /');
        }
        if ($item === $this->pattern) {
            return true;
        }
        if (is_string($item) && is_string($this->pattern)) {
            preg_match("~^{$this->pattern}~x", $item, $matches);
            $this->getArgumentsFromString($item);
            if (!empty($matches)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Set the Resolvable that is to be used in case this function conks out
     *
     * @param \Sm\Abstraction\Resolvable\Resolvable $DefaultResolvable
     *
     * @return \Sm\Routing\Route
     */
    public function setDefaultResolution(Resolvable $DefaultResolvable): Route {
        $this->DefaultResolvable = $DefaultResolvable;
        return $this;
    }
    public function setResolution(Resolvable $resolvable = null): Route {
        $this->Resolution = $resolvable;
        return $this;
    }
    /**
     * Resolve the Route.
     * This takes either an "Arguments" object or just the arguments passed in as arguments.
     * The First argument is assumed to be a Request
     *
     * @param \Sm\Abstraction\Request\Request $Request ,..
     *
     * @return mixed
     * @throws \Sm\Resolvable\Error\UnresolvableError
     * @throws \Sm\Routing\MalformedRouteException
     */
    public function resolve($Request = null) {
        try {
            if (!($Request instanceof Request)) {
                throw new MalformedRouteException("Cannot resolve request.");
            } else if (!($this->Resolution instanceof Resolvable)) {
                throw new UnresolvableError("No way to resolve request.");
            }
    
            if ($this->matches($Request)) {
                $arguments = $this->getArgumentsFromString($Request->getUrlPath());
                array_unshift($arguments, $Request);
                $res = $this->Resolution->resolve(...array_values($arguments));
                return $res;
            } else {
                throw new UnresolvableError("Cannot match the route");
            }
        } catch (\Exception $e) {
            if ($e instanceof MalformedRouteException) {
                throw $e;
            }
            if (isset($this->DefaultResolvable)) {
                return $this->DefaultResolvable->resolve($Request);
            }
            if ($e instanceof UnresolvableError) {
                throw $e;
            }
        }
        throw new UnresolvableError("Cannot resolve route");
    }
    public function __debugInfo() {
        return $this->jsonSerialize();
    }
    public function jsonSerialize() {
        return [
            $this->pattern,
            is_object($this->Resolution) ? get_class($this->Resolution) : $this->Resolution,
            StringResolvable::coerce($this->Resolution),
        ];
    }
    public static function coerce($item) {
        if ($item instanceof Route) {
            return $item;
        }
        if (is_array($item)) {
            $resolution = $item['resolution'] ?? null;
            $pattern    = $item['pattern'] ?? null;
            $default    = $item['default'] ?? null;
            if (count($item) === 1 && !$resolution && !$pattern) {
                $k          = key($item);
                $resolution = $item[ $k ];
                $pattern    = $k;
            }
            if (!($resolution && $pattern)) {
                throw new MalformedRouteException("Malformed route configuration '{$pattern}''");
            }
            $Route = new static($resolution, $pattern);
            if ($default) {
                $Route->setDefaultResolution($default);
            }
        } else if (is_string($item)) {
            $Route = new static(null, $item);
        } else if ($item instanceof Resolvable) {
            $Route = new static($item);
        } else {
            throw new MalformedRouteException("Cannot coerce route");
        }
        return $Route;
    }
    
    #  protected
    ##############################################################################################
    public static function init($resolution = null, $pattern = null) {
        return new static($resolution, $pattern);
    }
    /**
     * This is a setter used when we want to set the "pattern" of the class to be a string.
     * This has to be used with a URL-like route pattern.
     *
     * examples:
     *  spwashi/{param_1}:[a-zA-Z_]+
     *  spwashi$
     *  spwashi/
     *
     * @param string $pattern
     *
     * @return string
     */
    protected function setStringPattern(string $pattern) {
        $pattern       = trim($pattern, ' \\/');
        $pattern_arr   = explode('/', $pattern);
        $fixed_pattern = '';
        $count         = 0;
        foreach ($pattern_arr as $portion) {
            $last_char = substr($portion, -1);
    
            preg_match("`\\{(.+)\\}:?(.+)?(/|$)`", $portion, $match);
            if (isset($match[1])) {
                $this->parameters[] = $match[1];
                if (!empty($match[2])) {
                    $portion = $match[2];
                } else {
                    $portion = '[a-zA-Z_]+[a-zA-Z_\d]*';
                }
                $portion = "($portion)";
            }
            
            if ($count) {
                if ($last_char === '*') {
                    $fixed_pattern .= "(?:$|{$portion}|/?$) ";
                } else {
                    $fixed_pattern .= "{$portion}";
                }
            } else {
                $fixed_pattern = $portion;
            }
            $fixed_pattern .= '(?:/|$)   ';
            ++$count;
        }
        return $this->pattern = $fixed_pattern;
    }
    private function getArgumentsFromString(string $item) {
        preg_match("~^{$this->pattern}~x", $item, $matches);
        if (!count($matches)) {
            return [];
        }
        array_shift($matches);
        $Arguments = [];
        foreach ($this->parameters as $parameter_name) {
            if (!count($matches)) {
                continue;
            }
            $parameter_value = array_shift($matches);
    
            if ($parameter_name) {
                $Arguments[ $parameter_name ] = $parameter_value;
            } else {
                $Arguments[] = $parameter_value;
            }
        }
        if (count($matches)) {
            $Arguments = array_merge($Arguments, $matches);
        }
        return $Arguments;
    }
}