<?php
/**
 * User: Sam Washington
 * Date: 3/5/17
 * Time: 3:40 PM
 */

namespace Sm\Formatter;


use Sm\Abstraction\Formatting\Formatter;
use Sm\Abstraction\Identifier\Identifiable;
use Sm\Factory\Factory;
use Sm\Resolvable\FunctionResolvable;
use Sm\Resolvable\StringResolvable;
use Sm\Storage\Container\Container;
use Sm\Util;

/**
 * Class FormatterFactory
 *
 * @property null|\Sm\Storage\Container\Container Aliases
 * @package   Sm\Formatter
 */
class FormatterFactory extends Factory {
    protected $Aliases;
    /** @var  Container $Rules For cases when we are going to provide some temporary Formatting modifications to something, this is the container for that */
    protected $Rules;
    /** @var  array $registered_rule_names An array of the names of Rules that are being applied */
    protected $registered_rule_names = [];
    protected $used_rule_names       = [];
    
    /**
     * FormatterFactory constructor.
     */
    public function __construct() {
        $this->Aliases = new Container;
        $this->Rules   = (new Container)->setConsumptionMode(true);
        parent::__construct();
    }
    public function __get($name) {
        if ($name === 'Aliases') {
            return $this->Aliases;
        }
        return parent::__get($name);
    }
    
    /**
     * @param string                             $name
     * @param string|FunctionResolvable|callable $resolvable
     *
     * @return $this
     */
    public function addRule(string $name, $resolvable = null) {
        if (!in_array($name, $this->registered_rule_names)) {
            $this->registered_rule_names[] = $name;
        }
        if (!isset($resolvable)) {
            return $this;
        }
        if (!($resolvable instanceof FunctionResolvable) && !($resolvable instanceof StringResolvable)) {
            $resolvable = is_callable($resolvable) ? FunctionResolvable::coerce($resolvable) : StringResolvable::coerce($resolvable);
        }
        $this->Rules->register($name, $resolvable);
        return $this;
    }
    /**
     * Remove a formatting rule
     *
     * @param string $name
     *
     * @return $this
     */
    public function removeRule(string $name) {
        $index = array_search($name, $this->registered_rule_names);
        if ($index < 0) {
            return $this;
        } else {
            array_splice($this->registered_rule_names, $index);
        }
        return $this;
    }
    /**
     * Make it so that we know we can use the previously "consumed" rules.
     *
     * @return $this
     */
    public function restoreRules() {
        $this->used_rule_names = [];
        return $this;
    }
    public function canCreateClass($object_type) {
        return is_a($object_type, Formatter::class, true);
    }
    /**
     * @param null $item
     *
     * @return null
     */
    public function build($item = null) {
        $result = parent::build($item, $this);
        return PlainStringFormatter::coerce($result);
    }
    public function format($item_to_format) {
        $rule_cache_key = Util::generateRandomString(4);
        $this->Rules->Cache->start($rule_cache_key);
        
        # true if the Rules Cache was started by this function call
        $key_matches = $this->Rules->Cache->keyMatches($rule_cache_key);
        
        
        $item = $this->consumeRules($item_to_format, true);
        
        # Format arrays individually
        if (is_array($item)) {
            $formatted_item = [];
            foreach ($item as $index => $value) {
                $formatted_item[ $index ] = $this->format($value);
            }
            return $formatted_item;
        }
        
        
        # Build the item like a factory
        $built_item = $this->build($item);
        $result     = $this->consumeRules($built_item, false);
        
        if ($result === $built_item || $result instanceof Formatter) {
            return $result;
        }
        
        $result = $this->build($result);
        
        # If this function call was what started the Rules cache
        if ($key_matches) {
            $this->Rules->resetConsumedItems();
            $this->Rules->Cache->end($rule_cache_key);
        }
        
        # Make the rules usable again
        $this->restoreRules();
        
        return $result;
    }
    /**
     * For anything we want to apply Rules to, apply them and return the result.
     * These are usually formatting quirks, like every time we see a Property, we might want to return a PropertyAsColumnFragment, etc.
     *
     * @param      $result
     *
     * @param bool $is_item Are we applying the rules on this item
     *
     * @return mixed
     */
    protected function consumeRules($result, $is_item = false) {
        # Apply any formatting rules
        $count = count($this->registered_rule_names);
        for ($index = 0; $index < $count; $index++) {
            $name = $this->registered_rule_names[ $index ];
            
            # Used Rule Names is an array, indexed by rule_name, of arrays, indexed by the "shape" of the result, containing
            $this->used_rule_names[ $name ] = $this->used_rule_names[ $name ] ?? [];
            $arg_shape                      = Util::getShapeOfItem($result);
            if (in_array($arg_shape, $this->used_rule_names)) {
                continue;
            }
            #if (in_array($name, $this->used_rule_names)) continue;
            
            # If the rule applies, set the result to whatever it says it should be
            $formatted = $this->Rules->resolve($name, $result, $is_item);
            
            # If the result is an empty string, maybe we shouldn't count it?
//            if (is_string($formatted) && empty($formatted)) $formatted = null;
            
            if (!isset($formatted)) {
                continue;
            }
            
            $result = $formatted;
            
            # Mark this rule as "used" so we don't call it more than once.
            $this->used_rule_names[ $name ][] = $arg_shape;
        }
        return $result;
    }
    /**
     * Get the name of what a Fragment should be in the Fragment registry
     *
     * @param \Sm\Abstraction\Identifier\Identifiable $item
     * @param                                         $fragment_type
     *
     * @return string
     */
    protected function getFragmentName(Identifiable $item, $fragment_type) {
        return "{$fragment_type}|{$item->getObjectId()}";
    }
    protected static function resultIsComplete($item) {
        return is_array($item) ? $item : $item instanceof Formatter;
    }
}