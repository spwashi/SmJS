<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:28 PM
 */

namespace Sm\Condition;


use Sm\Abstraction\Formatting\Formattable;
use Sm\Resolvable\Resolvable;

abstract class Condition extends Resolvable implements Formattable {
    protected $registry = [];
    
    public function __construct() {
        parent::__construct(null);
        $evaluator = $this->getDefaultEvaluator();
        $this->register(\Closure::bind($evaluator, $this));
    }
    /**
     * Get the end value of a component
     *
     * @param $component
     *
     * @return mixed|\Sm\Condition\Condition
     */
    public function valueOf($component) {
        if ($component instanceof Resolvable) {
            $component->setResolvableFactory($this->getResolvableFactory());
            if ($component instanceof Condition) {
            
            }
        }
        return $component instanceof Resolvable ? $component->resolve() : $component;
    }
    /**
     * Return the final result of the Resolvable (as of now)
     *
     * @return mixed
     */
    public function resolve() {
        foreach ($this->registry as $key => $item) {
            $result = $item($this->getVariables());
            if (isset($result)) return $result;
        }
        return null;
    }
    /**
     * Set a function that can be used to evaluate the truthfulness of a condition
     *
     * @param callable $item
     */
    public function register(callable $item) {
        array_unshift($this->registry, $item);
    }
    /**
     * Method called in the constructor that returns the default function to use to evaluate the Condition
     *
     * @return mixed
     */
    abstract protected function getDefaultEvaluator(): callable;
}