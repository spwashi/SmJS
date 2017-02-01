<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 2:38 AM
 */

namespace Sm\Routing;


use Sm\Abstraction\Coercable;
use Sm\Abstraction\Request\Request;
use Sm\Abstraction\Resolvable\Arguments;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Resolvable\StringResolvable;

class Route implements Resolvable, Coercable {
    /** @var  Resolvable $DefaultResolvable */
    protected $DefaultResolvable;
    /** @var  Resolvable $Resolution */
    protected $Resolution;
    protected $pattern;
    protected $parameters = [ ];
    
    public function __construct($resolution = null, $pattern = null) {
        if (is_string($pattern) || is_numeric($pattern)) $this->setStringPattern($pattern);
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
        if ($item instanceof Request) $item = $item->getUrlPath();
        if (is_string($item)) $item = trim($item, '\\ /');
        if ($item === $this->pattern) return true;
        if (is_string($item) && is_string($this->pattern)) {
            preg_match("~^{$this->pattern}~x", $item, $matches);
            $this->getArgumentsFromString($item);
            if (!empty($matches)) return true;
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
    public function setDefaultResolution(Resolvable $DefaultResolvable) :Route {
        $this->DefaultResolvable = $DefaultResolvable;
        return $this;
    }
    public function setResolution(Resolvable $resolvable = null) :Route {
        $this->Resolution = $resolvable;
        return $this;
    }
    /**
     * Resolve the Route.
     * This takes either an "Arguments" object or just the arguments passed in as arguments.
     * The First argument is assumed to be a Request
     *
     * @param \Sm\Abstraction\Request\Request $request ,..
     *
     * @return mixed
     */
    public function resolve(Request $request = null) {
        try {
            if (!($request instanceof Request)) {
                throw new MalformedRouteException("Cannot resolve request.");
            } else if (!($this->Resolution instanceof Resolvable)) {
                throw new UnresolvableError("No way to resolve request.");
            }
    
            if ($this->matches($request)) {
                $Arguments = $this->getArgumentsFromString($request->getUrlPath());
                $Arguments->unshift($request, 'Request');
                return $this->Resolution->resolve($Arguments);
            } else {
                throw new UnresolvableError("Cannot match the route");
            }
        } catch (\Exception $e) {
            if ($e instanceof MalformedRouteException) throw $e;
            if (isset($this->DefaultResolvable)) return $this->DefaultResolvable->resolve($request);
            if ($e instanceof UnresolvableError) throw $e;
        }
        throw new UnresolvableError("Cannot resolve route");
    }
    public function __debugInfo() {
        return [
            $this->pattern,
            is_object($this->Resolution) ? get_class($this->Resolution) : $this->Resolution,
            StringResolvable::coerce($this->Resolution),
        ];
    }
    public static function coerce($item) {
        if ($item instanceof Route) return $item;
        if (is_array($item)) {
            $resolution = $item['resolution'] ?? null;
            $pattern    = $item['pattern'] ?? null;
            $default    = $item['default'] ?? null;
            if (count($item) === 1 && !$resolution && !$pattern) {
                $k          = key($item);
                $resolution = $item[ $k ];
                $pattern    = $k;
            }
            if (!($resolution && $pattern)) throw new MalformedRouteException("Malformed route configuration {$pattern}");
            $Route = new static($resolution, $pattern);
            if ($default) $Route->setDefaultResolution($default);
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
        if (!count($matches)) return new Arguments();
        array_shift($matches);
        $Arguments = new Arguments;
        foreach ($this->parameters as $parameter_name) {
            if (!count($matches)) continue;
            $Arguments->push(array_shift($matches), $parameter_name);
        }
        if (count($matches)) $Arguments->push($matches);
        return $Arguments;
    }
    private static function enforce_length($len, $str) {
        $_strlen = strlen($str);
        if ($_strlen < $len) {
            $str .= str_repeat(' ', $len - $_strlen);
        } else if ($_strlen > $len) {
            $str = substr($str, 0, $len);
        }
        return $str;
    }
}