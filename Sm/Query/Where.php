<?php
/**
 * User: Sam Washington
 * Date: 3/23/17
 * Time: 11:20 AM
 */

namespace Sm\Query;


use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\Constructs\Or_;
use Sm\EvaluableStatement\EqualityCondition\EqualToCondition;
use Sm\EvaluableStatement\EqualityCondition\GreaterThanCondition;
use Sm\EvaluableStatement\EvaluableStatementFactory;
use Sm\Factory\FactoryContainer;

/**
 * Class Where
 *
 * @package Sm\Query
 * @method static Where greater_($left, $right);
 */
class Where {
    /** @var  FactoryContainer */
    protected $FactoryContainer;
    protected $Condition;
    
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
    public function greater($left, $right) {
        $this->Condition = $this->createEqualityCondition(GreaterThanCondition::class, ...func_get_args());
        return $this;
    }
    public function equals($left, $right) {
        $this->Condition = $this->createEqualityCondition(EqualToCondition::class, ...func_get_args());
        return $this;
    }
    
    public function _and($condition) {
        $this->Condition = $this->createChainableConstruct(And_::class, $condition);
        return $this;
    }
    public function _or($condition) {
        $this->Condition = $this->createChainableConstruct(Or_::class, $condition);
        return $this;
    }
    /**
     * Get the underlying Condition that we are trying to format.
     *
     * @return \Sm\EvaluableStatement\EvaluableStatement|null
     */
    public function getCondition() {
        return $this->Condition;
    }
    
    
    public static function __callStatic($name, $arguments) {
        $len      = strlen($name);
        $property = substr($name, 0, $len - 1);
        $instance = new static;
        if (method_exists($instance, $property)) return call_user_func_array([ $instance, $property ], $arguments);
        return null;
    }
    public static function init() {
        return new static;
    }
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
    protected function createChainableConstruct(string $classname, $condition) {
        $OverallCondition = $this->Condition;
        if (!$this->Condition) $OverallCondition = true;
        $ChainableConstruct = $this->EvaluableStatementFactory()->build($classname);
        $ChainableConstruct->set($OverallCondition, static::val($condition));
        return $ChainableConstruct;
    }
    protected static function val($item) {
        if ($item instanceof Where) return $item->getCondition();
        return $item;
    }
}
