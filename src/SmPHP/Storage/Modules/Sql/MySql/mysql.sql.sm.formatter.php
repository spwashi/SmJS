<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 3:04 PM
 */

use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormatterFactory;
use Sm\Core\Formatting\Fragment\ArrayFragment;
use Sm\Core\Internal\Identification\Identifier;
use Sm\Core\Resolvable\ArrayResolvable;
use Sm\Core\Resolvable\NativeResolvable;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Data\Property\Property;
use Sm\Data\Type\Variable_\Variable_;
use Sm\Process\EvaluableStatement\Constructs\And_;
use Sm\Process\EvaluableStatement\Constructs\ChainableConstruct;
use Sm\Process\EvaluableStatement\EqualityCondition\EqualityCondition_;
use Sm\Storage\Database\TableSource;
use Sm\Storage\Modules\Sql\Formatter\ColumnAsDefinitionFragment;
use Sm\Storage\Modules\Sql\Formatter\CreateTableFragment;
use Sm\Storage\Modules\Sql\Formatter\DeleteFragment;
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
use Sm\Storage\Modules\Sql\Formatter\SourcesArrayFragment;
use Sm\Storage\Modules\Sql\Formatter\UpdateFragment;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;

return [
    #
    ## Native types, defaults
    #
    function ($item) {
        if (is_bool($item)) return $item ? 'true' : 'false';
        if (is_null($item)) return 'NULL';
        if (is_scalar($item)) return is_string($item) ? "'$item'" : $item;
        
        $item_type = is_object($item) ? get_class($item) : gettype($item);
        throw new UnimplementedError("Cannot properly format item of type \"{$item_type}\".");
    },
    ArrayFragment::class              => function (ArrayFragment $ArrayFragment, FormatterFactory $FormatterFactory) {
        return json_encode($ArrayFragment->getArray());
    },
    
    #
    ## Variables or Resolvables
    #
    ArrayResolvable::class            => function (ArrayResolvable $ArrayResolvable, FormatterFactory $FormatterFactory) {
        $array         = $ArrayResolvable->resolve();
        $ArrayFragment = ArrayFragment::init()->setArray($array);
        return $FormatterFactory->format($ArrayFragment);
    },
    StringResolvable::class           => function ($item) { return $item; },
    Variable_::class                  => function (Variable_ $item) { return ":{$item->name}"; },
    
    #
    ## Sources
    #
    TableSource::class                => function (TableSource $TableSource, FormatterFactory $FormatterFactory) {
        $name = $TableSource->getName();
        return "{$name}";
    },
    SourceFragment::class             => function (SourceFragment $SourceFragment, FormatterFactory $FormatterFactory) {
        $Source         = $SourceFragment->getSource();
        $SourceObjectId = $Source->getObjectId();
        # This is confusing because it should have been written as a rule.
        #  basically, we assume that SourceFragments are always going to be given to us as something readable (right now, only SourceAsAliasFragments)
        #  and says "when we encounter raw SourceFragments, replace them later with whatever their alias is"
        #  I would prefer to not do this, but I want to reach my deadline and I'll probably read this later and remember todo look into cleaning this up
        $FormatterFactory->Aliases->register($SourceObjectId, $Source);
        return $SourceObjectId;
    },
    SourceAsAliasFragment::class      => function (SourceAsAliasFragment $SourceAsAliasFragment, FormatterFactory $FormatterFactory) {
        $Source                  = $SourceAsAliasFragment->getSource();
        $PropertyHaver_object_id = $SourceAsAliasFragment->getPropertyHaverObjectId();
        $Source_object_id        = $Source->getObjectId();
        $name_as_alias           = str_replace("object:", "alias:", $Source_object_id);
        
        
        $Identifier = Identifier::combineObjectIds($Source_object_id, $PropertyHaver_object_id??'');
        $alias      = $FormatterFactory->Aliases->resolve($Identifier);
        
        if (!isset($alias)) {
            return $FormatterFactory->format(SourceFragment::inherit($SourceAsAliasFragment));
        }
        $alias = $alias ?? $Source->getName();
        $FormatterFactory->Aliases->register($name_as_alias, $alias);
        return $name_as_alias;
    },
    
    #
    ## Properties
    #
    Property::class                   => function (Property $Property, FormatterFactory $FormatterFactory) {
        if (!$FormatterFactory->Aliases->canResolve($Property->getObjectId())) {
            $FormatterFactory->Aliases->register($Property->getObjectId(), $Property->name);
        }
        return "{$Property}";
    },
    PropertyAsValueFragment::class    => function (PropertyAsValueFragment $PropertyAsValueFragment, FormatterFactory $FormatterFactory) {
        $object_id = $PropertyAsValueFragment->getProperty()->object_id;
        # This is to be used for binding parameters
        $name_as_alias = str_replace("object:", "value:", $object_id);
        $alias         = $FormatterFactory->format(PropertyAsAliasFragment::inherit($PropertyAsValueFragment));
        $FormatterFactory->Aliases->register($name_as_alias, ":{$alias}");
        return $name_as_alias;
    },
    PropertyAsNameFragment::class     => function (PropertyAsNameFragment $PropertyAsNameFragment, FormatterFactory $FormatterFactory) {
        $Property = $PropertyAsNameFragment->getProperty();
        return "`$Property->name`";
    },
    PropertyAsAliasFragment::class    => function (PropertyAsAliasFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property      = $PropertyFragment->getProperty() ?? null;
        $object_id     = $Property->object_id;
        $source_name   = $FormatterFactory->format($PropertyFragment->getSourceFragment());
        $name_as_alias = str_replace("object:", "alias:", $object_id);
        $FormatterFactory->Aliases->register($name_as_alias, "{$source_name}_" . $FormatterFactory->format($Property));
        return $name_as_alias;
    },
    PropertyAsColumnFragment::class   => function (PropertyAsColumnFragment $PropertyFragment, FormatterFactory $FormatterFactory) {
        /** @var Property $Property */
        $Property       = $PropertyFragment->getFormattedAttributes()['Property'] ?? null;
        $object_id      = $Property->object_id;
        $SourceFragment = $PropertyFragment->getSourceFragment();
        $source_name    = $FormatterFactory->format($SourceFragment);
        $name_as_column = str_replace("object:",
                                      "column:",
                                      $object_id);
        
        
        $FormattedProperty = $FormatterFactory->format($Property);
        $source_name       = strpos($source_name, 'alias:') ? $source_name : "`{$source_name}`";
        $FormatterFactory->Aliases->register($name_as_column, "{$source_name}.`$FormattedProperty`");
        return $name_as_column;
    },
    
    #
    ## Generic Query Aid
    #
    SourcesArrayFragment::class       => function (SourcesArrayFragment $SourcesArrayFragment, FormatterFactory $FormatterFactory) {
        $from_statement = '';
        $Fragments      = $SourcesArrayFragment->getSourceFragmentArray();
        /** @var SourceFragment $SourceFragment */
        foreach ($Fragments as $SourceFragment) {
            $table_name = $FormatterFactory->format(SourceFragment::inherit($SourceFragment));
            $alias      = $FormatterFactory->format(SourceAsAliasFragment::inherit($SourceFragment));
            
            $from_statement .= "\n\t\t`{$table_name}`";
            
            if ("$table_name" !== "$alias") $from_statement .= " AS {$alias}";
            
            $from_statement .= ",";
        }
        return trim($from_statement, "\n\t, ");
    },
    FromFragment::class               => function (FromFragment $FromFragment, FormatterFactory $FormatterFactory) {
        $from_statement  = "";
        $SourceFragments = $FromFragment->getSourceFragmentArray();
        $from_statement  = $FormatterFactory->format(SourcesArrayFragment::init()->setSourceFragmentArray($SourceFragments));
        return "FROM \t$from_statement";
    },
    WhereFragment::class              => function (WhereFragment $WhereFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $WhereFragment->getPropertyFragments();
        $Where             = $WhereFragment->getWhere();
        if (!isset($Where)) return '';
        $FormatterFactory->addRule('property_as_column', function ($item, $is_item = false) use ($FormatterFactory, $PropertyFragments) {
            if ($is_item && $item instanceof Property) {
                foreach ($PropertyFragments as $Fragment) {
                    # if the property is one of the Properties that we know about, return it as an aliased fragment
                    if ($item == $Fragment->getProperty()) {
                        return PropertyAsColumnFragment::inherit($Fragment);
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
    ColumnAsDefinitionFragment::class => function (ColumnAsDefinitionFragment $ColumnFragment, FormatterFactory $FormatterFactory) {
        $Property            = $ColumnFragment->getProperty();
        $name                = $Property->name;
        $null_statement      = !$ColumnFragment->canBeNull() ? "NOT NULL" : 'NULL';
        $data_type_statement = $ColumnFragment->getDataType();
        
        $default_statement = '';
        
        if ($ColumnFragment->hasDefaultValue()) {
            $default_value     = $FormatterFactory->format($ColumnFragment->getDefaultValue());
            $default_statement = "DEFAULT {$default_value}";
        }
        
        
        $statement_components = [
            $name,
            $data_type_statement,
            $null_statement,
            $default_statement,
        ];
        
        return join(' ', array_filter($statement_components));
    },
    #
    ## CRUD
    #
    InsertFragment::class             => function (InsertFragment $InsertFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments = $InsertFragment->getPropertyFragments();
        
        # This is the formatted string for the Source
        $source_statement = false;
        $Source           = null;
        # These are things that we are going to want to join and add to the query
        $property_statement_array = [];
        $first_row_of_properties  = [];
        foreach ($PropertyFragments as $index => $propertyFragment) {
            $_SourceFragment = $propertyFragment->getSourceFragment();
    
            # Get the DataSource if there hasn't been one set aside yet. This assumes that all of the Properties have the same DataSource,
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
            if ($item instanceof Property) {
                $item = PropertyAsValueFragment::init()->setProperty($item);
            }
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
    SelectFragment::class             => function (SelectFragment $SelectFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments     = $SelectFragment->getPropertyFragmentArray();
        $query_statement_array = [];
        foreach ($PropertyFragments as $property_id => $property_fragment) {
            $formatted_property      = $FormatterFactory->format(PropertyAsColumnFragment::inherit($property_fragment));
            $formatted_alias         = $FormatterFactory->format(PropertyAsAliasFragment::inherit($property_fragment));
            $query_statement_array[] = "{$formatted_property} AS {$formatted_alias}";
        }
        ##
        $from_statement  = $FormatterFactory->format($SelectFragment->getFromFragment());
        $where_statement = $FormatterFactory->format($SelectFragment->getWhereFragment());
        ##
        $query_statement = join(",\n\t\t", $query_statement_array);
        $query_statement = trim($query_statement, "\n\t,");
        return "SELECT \t{$query_statement}\n{$from_statement} \n{$where_statement}";
    },
    UpdateFragment::class             => function (UpdateFragment $UpdateFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments     = $UpdateFragment->getPropertyFragmentArray();
        $query_statement_array = [];
        /**
         * @var string           $property_id
         * @var PropertyFragment $property_fragment
         */
        foreach ($PropertyFragments as $property_id => $property_fragment) {
            $formatted_property      = $FormatterFactory->format(PropertyAsColumnFragment::inherit($property_fragment));
            $formatted_alias         = $FormatterFactory->format(PropertyAsValueFragment::inherit($property_fragment));
            $query_statement_array[] = "{$formatted_property} = {$formatted_alias}";
        }
        ##
        $sources_statement = $FormatterFactory->format($UpdateFragment->getSourcesArrayFragment());
        $where_statement   = $FormatterFactory->format($UpdateFragment->getWhereFragment());
        ##
        $query_statement = join(",\n\t\t", $query_statement_array);
        $query_statement = trim($query_statement, "\n\t,");
        return "UPDATE \t{$sources_statement} \nSET\t\t{$query_statement} \n{$where_statement}";
    },
    DeleteFragment::class             => function (DeleteFragment $DeleteFragment, FormatterFactory $FormatterFactory) {
        $PropertyFragments     = $DeleteFragment->getPropertyFragmentArray();
        $query_statement_array = [];
        foreach ($PropertyFragments as $property_id => $property_fragment) {
            $formatted_property      = $FormatterFactory->format(PropertyAsNameFragment::inherit($property_fragment));
            $query_statement_array[] = "{$formatted_property}";
        }
        ##
        $from_statement  = $FormatterFactory->format($DeleteFragment->getFromFragment());
        $where_statement = $FormatterFactory->format($DeleteFragment->getWhereFragment());
        ##
        $query_statement = join(",\n\t\t", $query_statement_array);
        $query_statement = trim($query_statement, "\n\t,");
        return "DELETE \t{$query_statement} \n{$from_statement} \n{$where_statement}";
    },
    CreateTableFragment::class        => function (CreateTableFragment $CreateTableFragment, FormatterFactory $FormatterFactory) {
        $SourceFragment         = $CreateTableFragment->getSourceFragment();
        $source_statement       = $FormatterFactory->format($SourceFragment);
        $source_name            = $SourceFragment->getSource();
        $create_table_statement = "CREATE TABLE {$source_statement} (\n\t\t";
        
        /** @var ColumnAsDefinitionFragment[] $ColumnFragmentArray */
        $ColumnFragmentArray    = $CreateTableFragment->getColumnFragmentArray();
        $column_statement_array = $FormatterFactory->format($ColumnFragmentArray);
        $create_table_statement .= join(",\n\t\t", $column_statement_array);
        
        $foreign_key_statement_array = [];
        $primary_key_array           = [];
        # region Foreign Key statements
        # If there are any properties that are actually references to other properties
        foreach ($ColumnFragmentArray as $_ColumnFragment) {
            $_ReferenceFragment = $_ColumnFragment->getReferenceFragment();
    
            if ($_ColumnFragment->isPrimaryKey()) {
                $primary_key_array[] = $_ColumnFragment->getProperty()->name;
            }
            
            if (!isset($_ReferenceFragment)) continue;
            $_column_name          = $_ColumnFragment->getProperty()->name;
            $_Reference            = $_ReferenceFragment->getProperty();
            $_ReferenceSource      = $_Reference->getSource();
            $_reference_table_name = $_ReferenceSource->getName();
            
            if (!($_ReferenceSource instanceof TableSource)) {
                #todo consider throwing an error?
                continue;
            }
            
            if (!isset($_reference_table_name)) {
                throw new UnimplementedError("Cannot reference sources that do not have a name");
            }
            $_reference_name                = $_Reference->name;
            $foreign_key_statement_array [] = "CONSTRAINT FK_{$_reference_table_name}__{$source_name}__{$_column_name} FOREIGN KEY ({$_column_name}) REFERENCES {$_reference_table_name}({$_reference_name})";
        }
        # endregion
        $primary_keys_joined   = join(',', $primary_key_array);
        $primary_key_statement = "PRIMARY KEY ({$primary_keys_joined})";
        $foreign_key_statement = join(', ', array_filter($foreign_key_statement_array));
    
    
        if (strlen($primary_keys_joined)) $create_table_statement .= ",\n\t\t$primary_key_statement";
        if (strlen($foreign_key_statement)) $create_table_statement .= ",\n\t\t$foreign_key_statement";
        
        $create_table_statement .= "\n)";
        
        return $create_table_statement;
    },
    #
    ## And_, Or_, etc
    #
    ChainableConstruct::class         => function (ChainableConstruct $ConstructCondition, FormatterFactory $FormatterFactory) {
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
    NativeResolvable::class           => function (NativeResolvable $resolvable, FormatterFactory $FormatterFactory) {
        return $FormatterFactory->format($resolvable->resolve());
    },
    
    #
    ## =, >, <
    #
    EqualityCondition_::class         => function (EqualityCondition_ $Condition, FormatterFactory $FormatterFactory) {
        $left_side  = $FormatterFactory->format($Condition->left_side);
        $right_side = $FormatterFactory->format($Condition->right_side);
        $symbol     = $Condition->symbol;
        return "({$left_side} {$symbol} {$right_side})";
    },
];