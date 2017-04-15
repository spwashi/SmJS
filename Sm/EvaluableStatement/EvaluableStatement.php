<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 7:28 PM
 */

namespace Sm\EvaluableStatement;


use Sm\Abstraction\Formatting\Formattable;
use Sm\Formatter\FormatterFactory;
use Sm\Formatter\FormatterFactoryHaver;
use Sm\Resolvable\Resolvable;
use Sm\Type\Variable_\Variable_;

/**
 * Class EvaluableStatement
 *
 * Represents a statement that can be evaluated
 *
 * @package Sm\EvaluableStatement
 */
abstract class EvaluableStatement extends Resolvable implements Formattable, \JsonSerializable, FormatterFactoryHaver {
    protected $registry = [];
    /** @var  FormatterFactory $FormatterFactory The FormatterFactory that is to be used in formatting this Evaluable statement */
    protected $FormatterFactory;
    public function __construct() {
        parent::__construct(null);
    }
    /**
     * Check to see if there is a variable formatted like $_name_ in this class. If there is, return the value of that.
     * Otherwise, return null.
     *
     * @param $name
     *
     * @return mixed|null|\Sm\EvaluableStatement\EvaluableStatement
     */
    public function __get($name) {
        return $this->__call($name, false);
    }
    /**
     * Allow us to get formatted
     *
     * @param $name
     *
     * @param $arguments
     *
     * @return mixed|\Sm\EvaluableStatement\EvaluableStatement|string
     */
    public function __call($name, $arguments) {
        $var_name  = "_" . $name . "_";
        $do_format = count($arguments) ? $arguments[0] : true;
        return isset($this->$var_name)
            ? ($var_name === '_items_' ? $this->valueOfArray($this->$var_name, $do_format) : $this->valueOf($this->$var_name, $do_format))
            : null;
    }
    /**
     * Does this Evaluable Statement resolve to a discrete value?
     *
     * @return null|bool
     */
    public function resolvesToValue() {
        $variables = $this->getVariables();
        if (isset($variables['items']) && is_array($variables['items'])) {
            $variables += $variables['items'];
        }
        foreach ($variables as $index => $variable) {
            if ($variable instanceof Variable_ || $variable instanceof DeferredEvaluationStatement) {
                return null;
            }
        }
        return true;
    }
    /**
     * @inheritdoc
     * @return array
     */
    public function getVariables(): array {
        $vars = get_object_vars($this);
        $end  = [];
        
        # Loop through the variables and pick out the ones that look like $this->_variable_name_
        foreach ($vars as $index => $value) {
            $len = strlen($index);
            
            # Only include the variables that follow this naming scheme (sorry)
            if ($index[0] === '_' && $index[ $len - 1 ] === '_') {
                $new_index         = substr($index, 1, $len - 2);
                $end[ $new_index ] = $this->$index;
            }
        }
        return $end;
    }
    /**
     * Give us a way to set the variables after we've initialized the class
     *
     * @return mixed
     */
    abstract public function set(): EvaluableStatement;
    /**
     * This class sometimes resolves to a value, other times it resolves to a "statement".
     * These statements are very loosely defined and are meant to serve as wrappers for
     * EvaluableStatement results. Depending on the situation, this "statement" might need to be represented
     * in a certain format. This formatter allows us to make those modifications.
     *
     * For example, if there is a Condition that resolves to ['symbol'=>'>', 'left_side'=>1, 'right_side'=>Variable('sam')],
     * an SQL Formatter could stylize that as "1 > :sam"
     *
     *
     * @param \Sm\Formatter\FormatterFactory $FormatterFactory
     *
     * @return $this
     */
    public function setFormatterFactory(FormatterFactory $FormatterFactory) {
        $this->FormatterFactory = $FormatterFactory;
        return $this;
    }
    /**
     * Method to allow us to get the Value of an array
     *
     * @param      $component
     * @param null $format
     *
     * @return array
     */
    public function valueOfArray($component, $format = null) {
        $array = [];
        foreach ($component as $key => $item) {
            $array[ $key ] = $this->valueOf($item, $format);
        }
        return $array;
    }
    /**
     * Get the end value of a component
     *
     * @param      $component
     *
     * @param bool $format
     *
     * @return mixed|\Sm\EvaluableStatement\EvaluableStatement
     */
    public function valueOf($component, $format = false) {
        if ($component instanceof Resolvable) {
            $component->setFactoryContainer($this->getFactoryContainer());
        }
        if ($component instanceof Variable_) {
            return $component;
        }
        $result = $component instanceof Resolvable ? $component->resolve() : $component;
        return $result;
    }
    /**
     * Return the final result of the Resolvable (as of now)
     *
     * @return mixed
     */
    public function resolve() {
        $registry    = $this->registry;
        $can_resolve = $this->resolvesToValue();
        if ($can_resolve !== true) {
            return DeferredEvaluationStatement::coerce($this);
        }
    
        $registry[] = \Closure::bind($this->getDefaultEvaluator(), $this);
        foreach ($registry as $key => $item) {
            $result = $item($this);
            if (isset($result)) {
                return $result;
            }
        }
        return null;
    }
    /**
     * Set a function that can be used to evaluate the truthfulness of a condition
     *
     * @param callable|array $item
     *
     * @return $this
     */
    public function register($item) {
        if (is_callable($item)) {
            array_unshift($this->registry, $item);
        } else if (is_array($item)) {
            foreach ($item as $value) {
                $this->register($value);
            }
        }
        return $this;
    }
    public function jsonSerialize() {
        return $this->getVariables();
    }
    /**
     * Method called in the constructor that returns the default function to use to evaluate the EvaluableStatement
     *
     * @return mixed
     */
    abstract protected function getDefaultEvaluator(): callable;
}