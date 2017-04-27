<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 11:49 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Storage\Modules\Sql\Formatter\InsertFragment;

class InsertQuerySubInterpreter extends MysqlQuerySubInterpreter {
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        $PropertyFragments = $this->createPropertyFragmentArray();
        $InsertFragment    = InsertFragment::init()
                                           ->setPropertyFragments($PropertyFragments)
                                           ->setValueFragments($this->createValueFragments());
        return $InsertFragment;
    }
    private function createValueFragments() {
        $values_array_array = $this->Query->values;
        $Fragments          = [];
        foreach ($values_array_array as $index => $value_array) {
            $Fragments[] = $this->SqlModule->format($value_array);
        }
        return $Fragments;
    }
}