<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:04 PM
 */

use Sm\Abstraction\Identifier\Identifiable;
use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyHaver;
use Sm\Error\UnimplementedError;
use Sm\EvaluableStatement\Constructs\And_;
use Sm\EvaluableStatement\Constructs\ChainableConstruct;
use Sm\EvaluableStatement\EqualityCondition\EqualityCondition_;
use Sm\Formatter\FormatterFactory;
use Sm\Formatter\Fragment\ArrayFragment;
use Sm\Resolvable\ArrayResolvable;
use Sm\Resolvable\NativeResolvable;
use Sm\Resolvable\StringResolvable;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;
use Sm\Storage\Modules\Sql\Formatter\InsertFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsAliasFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsColumnFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsNameFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsValueFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SelectFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceAsAliasFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceFragment;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;
use Sm\Type\Variable_\Variable_;

return [
    function ($item) {
        if (is_bool($item)) {
            return $item ? 'true' : 'false';
        }
        if (is_null($item)) {
            return 'NULL';
        }
        if (is_scalar($item)) {
            return $item;
        }
        $item_type = is_object($item) ? get_class($item) : gettype($item);
        throw new UnimplementedError("Cannot properly format item of type \"{$item_type}\".");
    },
    function ($item, FormatterFactory $FormatterFactory) {
        if (!is_array($item)) return null;
        
        $end_array = [];
        foreach ($item as $key => $value) {
            if (!is_numeric($key)) {
                $key = $FormatterFactory->format(StringResolvable::coerce($key));
            }
            $value             = $FormatterFactory->format($value);
            $end_array["$key"] = $value;
        }
        return $end_array;
    },
    function ($item) {
        if (!is_string($item)) {
            return null;
        }
        
        # If this is an identifiable string, format it so it's a little bit prettier
        if (strpos($item, "{{object:") === 0) {
            return explode('|', $item)[1];
        }
        
        return "'$item'";
    },
    ArrayResolvable::class => function (ArrayResolvable $ArrayResolvable, FormatterFactory $FormatterFactory) {
        $array         = $ArrayResolvable->resolve();
        $Fragments     = $FormatterFactory->format($array);
        $ArrayFragment = ArrayFragment::init()->setFragments($Fragments);
        return $FormatterFactory->format($ArrayFragment);
    },
    ArrayFragment::class   => function (ArrayFragment $ArrayFragment, FormatterFactory $FormatterFactory) {
        $value = $ArrayFragment->getArray();
        return json_encode($value);
    },
    
    
    StringResolvable::class      => function ($item) { return $item; },
    Variable_::class             => function (Variable_ $item) { return ":{$item->name}"; },
    Property::class              => function (Property $Property, FormatterFactory $FormatterFactory) {
        if (!$FormatterFactory->Aliases->canResolve($Property->getObjectId())) {
            $FormatterFactory->Aliases->register($Property->getObjectId(), $Property->name);
        }
        return "{$Property}";
    },
    TableSource::class           => function (TableSource $TableSource, FormatterFactory $FormatterFactory) {
        $name = $TableSource->getName();
        return "{$name}";
    },
    SourceFragment::class        => function (SourceFragment $SourceFragment, FormatterFactory $FormatterFactory) {
        $Source         = $SourceFragment->getSource();
        $SourceObjectId = $Source->getObjectId();
        $FormatterFactory->Aliases->register($SourceObjectId, $Source);
        return $SourceObjectId;
    },
    SourceAsAliasFragment::class => function (SourceAsAliasFragment $SourceAsAliasFragment, FormatterFactory $FormatterFactory) {
        $Source           = $SourceAsAliasFragment->getSource();
        $Owner_object_id  = $SourceAsAliasFragment->getOwnerObjectId();
        $Source_object_id = $Source->getObjectId();
        $name_as_alias    = str_replace("object:", "alias:", $Source_object_id);
        
        
        $Identifier = Identifier::combineObjectIds($Source_object_id, $Owner_object_id??'');
        $alias      = $FormatterFactory->Aliases->resolve($Identifier);
        
        if (!isset($alias)) {
            return $FormatterFactory->format(SourceFragment::inherit($SourceAsAliasFragment));
        }
        $alias = $alias ?? $Source->getName();
        $FormatterFactory->Aliases->register($name_as_alias, $alias);
        return $name_as_alias;
    },
    
    PropertyAsValueFragment::class  => function (PropertyAsValueFragment $PropertyAsValueFragment, FormatterFactory $FormatterFactory) {
        $value     = $PropertyAsValueFragment->getProperty()->raw_value->resolve();
        $formatted = $FormatterFactory->format($value);
        return $formatted;
    },
    PropertyAsNameFragment::class   => function (PropertyAsNameFragment $PropertyAsNameFragment, FormatterFactory $FormatterFactory) {
        $Property = $PropertyAsNameFragment->getProperty();
        return "`$Property->name`";
    },
    PropertyAsAliasFragment::class  => function (PropertyAsAliasFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property      = $PropertyFragment->getProperty() ?? null;
        $object_id     = $Property->object_id;
        $source_name   = $FormatterFactory->format($PropertyFragment->getSourceFragment());
        $name_as_alias = str_replace("object:", "alias:", $object_id);
        $FormatterFactory->Aliases->register($name_as_alias, "{$source_name}_" . $FormatterFactory->format($Property));
        return $name_as_alias;
    },
    PropertyAsColumnFragment::class => function (PropertyAsColumnFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property       = $PropertyFragment->getVariables()['Property'] ?? null;
        $object_id      = $Property->object_id;
        $SourceFragment = $PropertyFragment->getSourceFragment();
        $source_name    = $FormatterFactory->format($SourceFragment);
        $name_as_column = str_replace("object:",
                                      "column:",
                                      $object_id);
        
        
        $FormattedProperty = $FormatterFactory->format($Property);
        $FormatterFactory->Aliases->register($name_as_column, "`{$source_name}`.`" . $FormattedProperty . "`");
        return $name_as_column;
    },
    
    PropertyHaver::class      => function (PropertyHaver $property_haver, FormatterFactory $FormatterFactory) {
        if ($property_haver instanceof Identifiable) {
            return $property_haver->getObjectId();
        }
        throw new UnimplementedError("There is no way to convert this PropertyHaver. ");
    },
    InsertFragment::class     => function (InsertFragment $InsertFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $InsertFragment->getPropertyFragments();
        
        # This is the formatted string for the Source
        $source_statement = false;
        $Source           = null;
        # These are things that we are going to want to join and add to the query
        $property_statement_array = [];
        $first_row_of_properties  = [];
        foreach ($PropertyFragments as $index => $propertyFragment) {
            $_SourceFragment = $propertyFragment->getSourceFragment();
            
            # Get the Source if there hasn't been one set aside yet. This assumes that all of the Properties have the same Source,
            if (!$source_statement) $source_statement = $FormatterFactory->format($_SourceFragment);
            
            # There should only be one source in the Source array, and it should be this one
            if (isset($Source) && ($Source !== ($Source = $_SourceFragment->getSource()))) throw new UnimplementedError("Cannot insert into multiple sources like this yet");
            
            $property_statement_array[] = $FormatterFactory->format(PropertyAsNameFragment::inherit($propertyFragment));
            $first_row_of_properties[]  = PropertyAsValueFragment::inherit($propertyFragment);
        }
        
        # region Format the Value Arrays
        
        $ValueFragment_arrays = $InsertFragment->getValueFragmentArrays();
        
        # prepend the array of the properties if it isn't empty
        array_unshift($ValueFragment_arrays, $first_row_of_properties);
        
        #   Only use nonempty array values
        $ValueFragment_arrays = array_filter($ValueFragment_arrays);
        #   Format all items, join in an array
        $ValueFragment_arrays = array_map(function ($item) use ($FormatterFactory) {
            !is_array($item) && var_dump($item);
            $item = $FormatterFactory->format($item);
            $item = join(', ', $item);
            return "({$item})";
        }, $ValueFragment_arrays);
        
        $value_statement = join(",\n\t\t", $ValueFragment_arrays);
        
        # endregion
        
        
        $property_statement = join(', ', $FormatterFactory->format($property_statement_array));
        
        $insert_statement = "INSERT INTO `{$source_statement}` \n\t\t({$property_statement}) \nVALUES \t{$value_statement}";
        
        return $insert_statement;
    },
    SelectFragment::class     => function (SelectFragment $SelectFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $SelectFragment->getPropertyFragments();
        $select_statement  = "";
        /**
         * @var string           $property_id
         * @var PropertyFragment $property_fragment
         */
        foreach ($PropertyFragments as $property_id => $property_fragment) {
            $formatted_property = $FormatterFactory->format(PropertyAsColumnFragment::inherit($property_fragment));
            $formatted_alias    = $FormatterFactory->format(PropertyAsAliasFragment::inherit($property_fragment));
            $select_statement   .= "\n\t\t{$formatted_property} AS {$formatted_alias},";
        }
        
        $from_statement   = $FormatterFactory->format($SelectFragment->getFrom());
        $WhereFragment    = $SelectFragment->getWhereFragment();
        $where_statement  = isset($WhereFragment) ? $FormatterFactory->format($WhereFragment) : $WhereFragment;
        $select_statement = trim($select_statement, "\n\t,");
        $select_statement = "SELECT \t{$select_statement}\n{$from_statement} \n{$where_statement}";
        
        return $select_statement;
    },
    WhereFragment::class      => function (WhereFragment $WhereFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $WhereFragment->getPropertyFragments();
        $Where             = $WhereFragment->getWhere();
        if (!isset($Where)) return '';
        
        $FormatterFactory->addRule('property_as_column', function ($item, $is_item = false) use ($FormatterFactory, $PropertyFragments) {
            if ($is_item && $item instanceof Property) {
                foreach ($PropertyFragments as $Fragment) {
                    # if the property is one of the Properties that we know about, return it as an aliased fragment
                    if ($item == $Fragment->getProperty()) {
                        return PropertyAsAliasFragment::inherit($Fragment);
                    }
                }
                # Otherwise, return it as a column (hope this works!)
                return PropertyAsColumnFragment::init()
                                               ->setProperty($item)
                                               ->setSourceFragment(SourceAsAliasFragment::init()->setSource($item->getSource()));
            }
            return null;
        });
        $where_statement = "WHERE \t" . $FormatterFactory->format($Where->getCondition());
        $FormatterFactory->removeRule('property_as_column');
        return $where_statement;
        
    },
    FromFragment::class       => function (FromFragment $FromFragment, FormatterFactory $FormatterFactory) {
        $from_statement = "";
        $aliases        = $FromFragment->getSourceFragmentArray();
        /** @var SourceFragment $SourceFragment */
        foreach ($aliases as $SourceFragment) {
            $table_name = $FormatterFactory->format(SourceFragment::inherit($SourceFragment));
            $alias      = $FormatterFactory->format(SourceAsAliasFragment::inherit($SourceFragment));
            
            $from_statement .= "\n\t\t`{$table_name}`";
            
            if ("$table_name" !== "$alias") $from_statement .= " AS {$alias}";
            
            $from_statement .= ",";
        }
        $from_statement = trim($from_statement, "\n\t, ");
        return "FROM \t$from_statement";
    },
    /**
     * AND_, OR_
     */
    ChainableConstruct::class => function (ChainableConstruct $ConstructCondition, FormatterFactory $FormatterFactory) {
        $items     = $ConstructCondition->items;
        $items     = $FormatterFactory->format($items);
        $construct = strtoupper($ConstructCondition->construct);
        
        if ($ConstructCondition instanceof And_) {
            $items =
                array_filter(
                    $items,
                    function ($item) { return "$item" !== "true"; });
        }
        
        
        $clause = join(" {$construct} ", $items);
        return count($items) > 1 ? "({$clause})" : "{$clause}";
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
        $symbol     = $Condition->symbol;
        return "({$left_side} {$symbol} {$right_side})";
    },
];