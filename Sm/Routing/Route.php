<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 2:38 AM
 */

namespace Sm\Routing;


use Sm\Abstraction\Request\Request;
use Sm\Abstraction\Resolvable\Arguments;
use Sm\Abstraction\Resolvable\Resolvable;
use Sm\Resolvable\Error\UnresolvableError;

class Route implements Resolvable {
    /** @var  Resolvable $DefaultResolvable */
    protected $DefaultResolvable;
    /** @var  Resolvable $subject */
    protected $subject;
    protected $pattern;
    protected $parameters = [ ];
    
    public function __construct($pattern = null) {
        if (is_string($pattern)) $this->setStringPattern($pattern);
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
    public function getArgumentsFromString(string $item) {
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
    public function setDefaultResolvable(Resolvable $DefaultResolvable) {
        $this->DefaultResolvable = $DefaultResolvable;
        return $this;
    }
    public function setSubject(Resolvable $resolvable = null) {
        $this->subject = $resolvable;
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
    public function resolve($request = null) {
        try {
            if (!($request instanceof Request)) {
                throw new UnresolvableError("Cannot resolve request.");
            } else if (!($this->subject instanceof Resolvable)) {
                throw new UnresolvableError("No way to resolve request.");
            }
            
            if ($this->matches($request))
                return $this->subject->resolve($this->getArgumentsFromString($request->getUrlPath()));
        } catch (\Exception $e) {
        } finally {
            if (isset($this->DefaultResolvable)) {
                return $this->DefaultResolvable->resolve($request);
            }
        }
        return null;
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