<?php
/**
 * User: Sam Washington
 * Date: 2/18/17
 * Time: 2:55 PM
 */

namespace Sm\EvaluableStatement;


use Sm\Factory\Factory;
use Sm\Util;

/**
 * Class EvaluableStatementFactory
 *
 * @package Sm\EvaluableStatement
 * @method EvaluableStatement build(...$class_name)
 * @method EvaluableStatement __invoke(...$class_name)
 */
class EvaluableStatementFactory extends Factory {
    protected $evaluators = [];
    /**
     * Add an evaluator to a class. This is here to make it easier to register handlers for a specific set of
     * EvaluableStatements.
     *
     * When we want an EvaluableStatement to be able to receive an "evaluator" function,
     * this allows us to dynamically attach them to the objects.
     *
     * @param $classname
     * @param $evaluator
     *
     * @return $this
     */
    public function registerEvaluatorForClass($classname, $evaluator) {
        # The EvaluableStatement will get an array of all of the evaluators that have to be registered
        $this->evaluators[ $classname ] = $this->evaluators[ $classname ] ?? [];
        if (is_callable($evaluator)) {
            # Add the evaluator function to the Evaluator
            $this->evaluators[ $classname ][] = $evaluator;
        } else if (is_array($evaluator)) {
            /** @var mixed $item */
            foreach ($evaluator as $item) {
                $this->registerEvaluatorForClass($classname, $item);
            }
        }
        return $this;
    }
    public function create_class(string $class_name, array $args = null) {
        /** @var EvaluableStatement $class */
        $class     = parent::create_class(...func_get_args());
        $parents   = Util::getAncestorClasses(get_class($class));
        $parents   = array_reverse($parents);
        $parents[] = $class_name;
        
        # Iterate through the ancestors of this class, allow us to use their evaluators
        foreach ($parents as $p_class_name) {
            if (!isset($this->evaluators[ $p_class_name ])) {
                continue;
            }
            $class->register($this->evaluators[ $p_class_name ]);
        }
        return $class;
    }
}