<?php
/**
 * User: Sam Washington
 * Date: 3/23/17
 * Time: 11:20 AM
 */

namespace Sm\Query;


use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\Constructs\ChainableConstruct;
use Sm\EvaluableStatement\Constructs\Or_;
use Sm\EvaluableStatement\EqualityCondition\EqualToCondition;
use Sm\EvaluableStatement\EqualityCondition\GreaterThanCondition;
use Sm\EvaluableStatement\EqualityCondition\LessThanCondition;
use Sm\EvaluableStatement\EvaluableStatementFactory;
use Sm\Factory\FactoryContainer;

/**
 * Class Where
 *
 * @package Sm\Query
 * @method static Where greater_($left, $right);
 * @method static Where less_($left, $right);
 * @method static Where equals_($left, $right);
 * @method static Where and_($condition);
 * @method static Where or_($condition);
 */
class Where {
    /** @var  FactoryContainer */
    protected $FactoryContainer;
    protected $Condition;
    protected $conditions            = [];
    protected $registered_conditions = [];
    
    /**
     * Get the EvaluableStatementFactory that this class will use to createEvaluableStatements
     *
     * @return \Sm\EvaluableStatement\EvaluableStatementFactory
     */
    public function EvaluableStatementFactory(): EvaluableStatementFactory {
        return $this->getFactoryContainer()->resolve(EvaluableStatementFactory::class);
    }
    public function getFactoryContainer() {
        return $this->FactoryContainer ?:
            $this->setFactoryContainer(new FactoryContainer)->getFactoryContainer();
    }
    /**
     * Set the FactoryContainer that this class will use to create other classes.
     *
     * @param FactoryContainer $FactoryContainer
     *
     * @return Where
     */
    public function setFactoryContainer(FactoryContainer $FactoryContainer) {
        $this->FactoryContainer = $FactoryContainer;
        return $this;
    }
    /**
     * Get the Where::$conditions array of this instance.
     *
     * @return array
     */
    public function getRawConditionsArray() {
        return $this->conditions;
    }
    /**
     * Add an array of EvaluableStatements to this "WHERE" clause
     *
     * @param array $Conditions
     *
     * @return $this
     */
    public function appendConditions(array $Conditions) {
        $this->conditions = array_merge($this->conditions, $Conditions);
        return $this;
    }
    /**
     * Get the underlying Condition that we are trying to format.
     *
     * @return \Sm\EvaluableStatement\EvaluableStatement|null
     */
    public function getCondition() {
        $AggregateCondition = null;
        /**
         * @var \Sm\EvaluableStatement\EvaluableStatement|ChainableConstruct $Condition
         */
        foreach ($this->conditions as $Condition) {
            if (!$AggregateCondition || ($AggregateCondition === true && !($Condition instanceof ChainableConstruct))) {
                $AggregateCondition = $Condition;
                continue;
            }
            if (!($Condition instanceof ChainableConstruct)) {
                $Condition = static::_and_($Condition);
            }
        
            $items = $Condition->items;
            if (!isset($items[0]))
                array_shift($items);
        
            $AggregateCondition = $Condition->set($AggregateCondition, ...$items);
        }
    
        return $AggregateCondition;
    }
    public function __call($name, $arguments) {
        $method = "_{$name}";
        if (method_exists($this, $method)) {
            $result = call_user_func_array([ $this, $method ], $arguments);
            $json   = json_encode($result);
            
            # Don't add the same Query twice
            if (isset($this->registered_conditions[ $json ])) return $this;
            $this->conditions[] = $this->registered_conditions[ $json ] = $result;
        }
        return $this;
    }
    public static function __callStatic($name, $arguments) {
        $instance = new static;
        return $instance->__call($name, $arguments);
    }
    /**
     * @return static
     */
    public static function init() { return new static; }
    /**
     * @param $classname
     * @param $left
     * @param $right
     *
     * @return \Sm\EvaluableStatement\EqualityCondition\EqualityCondition_
     */
    protected function createEqualityCondition(string $classname, $left, $right) {
        return $this->EvaluableStatementFactory()->build($classname)->set(static::val($left),
                                                                          static::val($right));
    }
    protected function createChainableConstruct(string $classname, $condition, $initial_value = null) {
        $ChainableConstruct = $this->EvaluableStatementFactory()->build($classname);
        if (empty($this->conditions)) $initial_value = null;
        $ChainableConstruct->set($initial_value, static::val($condition));
        return $ChainableConstruct;
    }
    protected static function val($item) {
        if ($item instanceof Where) return $item->getCondition();
        return $item;
    }
    
    # region queries
    private function _greater_($left, $right) {
        return $this->createEqualityCondition(GreaterThanCondition::class, ...func_get_args());
    }
    private function _less_($left, $right) {
        return $this->createEqualityCondition(LessThanCondition::class, ...func_get_args());
    }
    private function _and_($condition) {
        return $this->createChainableConstruct(And_::class, $condition);
    }
    private function _or_($condition) {
        return $this->createChainableConstruct(Or_::class, $condition);
    }
    private function _equals_($left, $right) {
        return $this->createEqualityCondition(EqualToCondition::class, ...func_get_args());
    }
    # endregion
}
