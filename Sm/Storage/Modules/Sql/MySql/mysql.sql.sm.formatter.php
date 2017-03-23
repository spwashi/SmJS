<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:04 PM
 */

use Sm\Abstraction\Identifier\Identifiable;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyHaver;
use Sm\Error\UnimplementedError;
use Sm\EvaluableStatement\Constructs\ChainableConstruct;
use Sm\EvaluableStatement\EqualityCondition\EqualityCondition_;
use Sm\Formatter\FormatterFactory;
use Sm\Resolvable\NativeResolvable;
use Sm\Resolvable\StringResolvable;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsColumnFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SelectFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceFragment;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;
use Sm\Storage\Source\Database\TableSource;
use Sm\Type\Variable_\Variable_;

return [
    function ($item) {
        if (is_bool($item)) return $item ? 'true' : 'false';
        if (is_null($item)) return 'NULL';
        if (is_scalar($item)) return $item;
        $item_type = is_object($item) ? get_class($item) : gettype($item);
        throw new UnimplementedError("Cannot properly format item of type \"{$item_type}\".");
    },
    function ($item, FormatterFactory $FormatterFactory) {
        if (is_array($item)) {
            $end_array = [];
            var_dump($end_array);
            foreach ($item as $key => $value) {
                $key               = $FormatterFactory->format($key);
                $value             = $FormatterFactory->format($value);
                $end_array["$key"] = $value;
            }
            return $end_array;
        }
    },
    function ($item) {
        if (is_string($item)) {
            # If this is an identifiable string, format it so it's a little bit prettier
            if (strpos($item, "{{object:") === 0) {
                return explode('|', $item)[1];
            }
        }
        return null;
    },
    StringResolvable::class => function ($item) { return $item; },
    Variable_::class        => function (Variable_ $item) { return ":{$item->name}"; },
    Property::class         => function (Property $property, FormatterFactory $FormatterFactory) {
        /** @var PropertyFragment $Fragment */
        $Fragment = $FormatterFactory->getFragment($property, PropertyAsColumnFragment::class);
        if ($Fragment) $property = $FormatterFactory->format($Fragment);
        return "{$property}";
    },
    TableSource::class      => function (TableSource $TableSource) {
        $name = $TableSource->getName();
        return "{$name}";
    },
    SourceFragment::class   => function (SourceFragment $SourceFragment) {
        $source_name = $SourceFragment->getSourceAlias() ?? $SourceFragment->getSource()->getName();
        return $source_name;
    },
    
    PropertyFragment::class         => function (PropertyFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property       = $PropertyFragment->getVariables()['Property'] ?? null;
        $object_id      = $Property->object_id;
        $source_name    = $FormatterFactory->format($PropertyFragment->getSourceFragment());
        $name_as_column = str_replace("object:",
                                      "alias:",
                                      $object_id);
        $FormatterFactory->Aliases->register($name_as_column,
                                             "{$source_name}_{$object_id}");
        return $name_as_column;
    },
    PropertyAsColumnFragment::class => function (PropertyAsColumnFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property       = $PropertyFragment->getVariables()['Property'] ?? null;
        $object_id      = $Property->object_id;
        $source_name    = $FormatterFactory->format($PropertyFragment->getSourceFragment());
        $name_as_column = str_replace("object:",
                                      "column:",
                                      $object_id);
        $FormatterFactory->Aliases->register($name_as_column,
                                             "`{$source_name}`.`{$object_id}`");
        return $name_as_column;
    },
    
    PropertyHaver::class => function (PropertyHaver $property_haver, FormatterFactory $FormatterFactory) {
        if ($property_haver instanceof Identifiable) {
            return $property_haver->getObjectId();
        }
        throw new UnimplementedError("There is no way to convert this PropertyHaver. ");
    },
    
    SelectFragment::class     => function (SelectFragment $SelectFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $SelectFragment->getProperties();
        $select_statement  = "";
        /** @var Property[] $aliased */
        $aliased = [];
        /**
         * @var string           $property_id
         * @var PropertyFragment $property_fragment
         */
        foreach ($PropertyFragments as $property_id => $property_fragment) {
            $Property             = $property_fragment->getProperty();
            $aliased["$Property"] = $Property->name;
            
            $formatted_property = $FormatterFactory->format(PropertyAsColumnFragment::inherit($property_fragment));
            $formatted_alias    = $FormatterFactory->format(PropertyFragment::inherit($property_fragment));
            $select_statement   .= "\n  {$formatted_property} AS {$formatted_alias},";
        }
        
        $from_statement   = $FormatterFactory->format($SelectFragment->getFrom());
        $where_statement  = $FormatterFactory->format($SelectFragment->getWhere());
        $select_statement = "SELECT " . trim($select_statement, "\n ,") . "\n{$from_statement} \n{$where_statement}";
        
        foreach ($aliased as $alias => $real_name) {
            $select_statement = str_replace("$alias", $real_name, $select_statement);
        }
        return $select_statement;
    },
    WhereFragment::class      => function (WhereFragment $WhereFragment, FormatterFactory $FormatterFactory) {
        $where_statement = "WHERE " . $FormatterFactory->format($WhereFragment->getWhere()->getCondition());
        return $where_statement;
        
    },
    FromFragment::class       => function (FromFragment $FromFragment, FormatterFactory $FormatterFactory) {
        $from_statement = "";
        $aliases        = $FromFragment->getAliases();
        foreach ($aliases as $alias => $TableSource) {
            $formatted_alias = $FormatterFactory->format($TableSource);
            $from_statement  .= "\n  {$formatted_alias} ";
            
            if ("$formatted_alias" !== "$alias") $from_statement .= " AS {$alias}";
            
            $from_statement .= ",";
        }
        $from_statement = trim($from_statement, "\n, ");
        return "FROM $from_statement";
    },
    /**
     * AND_, OR_
     */
    ChainableConstruct::class => function (ChainableConstruct $ConstructCondition, FormatterFactory $FormatterFactory) {
        $items  = $ConstructCondition->items;
        $items  = $FormatterFactory->format($items);
        $name   = strtoupper($FormatterFactory->format($ConstructCondition->construct));
        $clause = join(" {$name} ", $items);
        return "({$clause})";
    },
    
    NativeResolvable::class   => function (NativeResolvable $resolvable, FormatterFactory $FormatterFactory) {
        return $FormatterFactory->format($resolvable->resolve());
    },
    /**
     * =, >, <
     */
    EqualityCondition_::class => function (EqualityCondition_ $Condition, FormatterFactory $FormatterFactory) {
        $right_side = $FormatterFactory->format($Condition->right_side);
        $left_side  = $FormatterFactory->format($Condition->left_side);
        $symbol     = $FormatterFactory->format($Condition->symbol);
        return "({$left_side} {$symbol} {$right_side})";
    },
];