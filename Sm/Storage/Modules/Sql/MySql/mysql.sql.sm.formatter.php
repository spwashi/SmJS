<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:04 PM
 */

use Sm\Entity\Property\Property;
use Sm\EvaluableStatement\Constructs\ChainableConstruct;
use Sm\EvaluableStatement\EqualityCondition\EqualityCondition_;
use Sm\Type\Variable_\Variable_;

return [
    function ($item) {
        if (is_bool($item)) return $item ? 'true' : 'false';
        return null;
    },
    Variable_::class          => function ($item) { return ":$item"; },
    Property::class           => function (Property $Property) {
        $Source = $Property->getSource();
        $name   = $Property->name;
        return "{$name}";
    },
    /**
     * AND_, OR_
     */
    ChainableConstruct::class => function (ChainableConstruct $ConstructCondition) {
        $items = $ConstructCondition->items();
        $name  = strtoupper($ConstructCondition->construct());
        return join(" {$name} ", $items);
    },
    /**
     * =, >, <
     */
    EqualityCondition_::class => function (EqualityCondition_ $Condition) {
        $right_side = $Condition->right_side();
        $left_side  = $Condition->left_side();
        $symbol     = $Condition->symbol();
        return "{$left_side} {$symbol} {$right_side}";
    },
];