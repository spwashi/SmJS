<?php
/**
 * User: Sam Washington
 * Date: 1/28/17
 * Time: 2:38 AM
 */

namespace Sm\Routing;


use Sm\Abstraction\Resolvable\Arguments;
use Sm\Abstraction\Resolvable\Resolvable;

class Route implements \Sm\Abstraction\Resolvable\Resolvable {
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
        $len           = count($pattern_arr);
        foreach ($pattern_arr as $portion) {
            $last_char = substr($portion, -1);
            
            preg_match("`\\{(.+)\\}:?(.*)?(/|$)`", $portion, $match);
            if (isset($match[1])) {
                $this->parameters[] = $match[1];
                if (isset($match[2])) {
                    $portion = $match[2];
                } else {
                    $portion = '[a-zA-Z_]+';
                }
                $portion = "($portion)";
            }
            if (!empty($match)) $this->parameters[] = $match[2]??null;
            
            if ($count) {
                if ($last_char === '*') {
                    $fixed_pattern .= "(?:$|/{$portion}(?:/|$)|/?$)";
                    continue;
                } else {
                    $fixed_pattern .= '/';
                }
            }
            $fixed_pattern .= $portion . '\b';
            ++$count;
        }
        return $this->pattern = $fixed_pattern;
    }
    private static function enforce_length($len, $str) {
        $_strlen = strlen($str);
        if ($_strlen < $len) {
            $str .= str_repeat(' ', $len - $_strlen);
        } else if ($_strlen > $len) {
            $str = substr(0, $len);
        }
        return $str;
    }
    public function getArgumentsFromString(string $item) {
        preg_match("~^{$this->pattern}~x", $item, $matches);
        if (!count($matches)) return new Arguments();
        
        array_shift($matches);
        $Arguments = new Arguments;
        foreach ($this->parameters as $parameter) {
            if (!count($matches)) continue;
            $Arguments->push($Arguments->$parameter = array_shift($matches));
        }
        if (count($matches)) $Arguments->push($matches);
        return $Arguments;
    }
    public function matches($item) {
        if (is_string($item)) $item = trim($item, '\\ /');
        if ($item === $this->pattern) return true;
        if (is_string($item) && is_string($this->pattern)) {
            preg_match("~^{$this->pattern}~x", $item, $matches);
            $this->getArgumentsFromString($item);
            if (!empty($matches)) return true;
        }
        return false;
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
     * @param Arguments|null|mixed $arguments ,..
     *
     * @return mixed
     */
    public function resolve($arguments = null) {
        $arguments =
            $arguments instanceof \Sm\Abstraction\Resolvable\Arguments
                ? $arguments
                : \Sm\Abstraction\Resolvable\Arguments::coerce(func_get_args());
        try {
            return $this->subject->resolve($arguments);
        } catch (\Exception $e) {
        } finally {
            if (isset($this->DefaultResolvable)) {
                return $this->DefaultResolvable->resolve($arguments);
            }
        }
        return null;
    }
}