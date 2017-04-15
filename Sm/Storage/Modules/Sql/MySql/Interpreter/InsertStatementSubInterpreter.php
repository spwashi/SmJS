<?php
/**
 * User: Sam Washington
 * Date: 4/13/17
 * Time: 11:49 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyHaver;
use Sm\Storage\Modules\Sql\Formatter\InsertFragment;

class InsertStatementSubInterpreter extends MysqlQuerySubInterpreter {
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        $PropertyFragments = $this->createPropertyFragments();
        $InsertFragment    = InsertFragment::init()
                                           ->setPropertyFragments($PropertyFragments)
                                           ->setValueFragments($this->createValueFragments());
        return $InsertFragment;
    }
    /**
     * @return Property[]|PropertyHaver[]
     */
    public function getQueryProperties() {
        return $this->Query->getInsertArray();
    }
    private function createValueFragments() {
        $values_array_array = $this->Query->getValuesArray();
        $Fragments          = [];
        foreach ($values_array_array as $index => $value_array) {
            $Fragments[] = $this->SqlModule->format($value_array);
        }
        return $Fragments;
    }
}