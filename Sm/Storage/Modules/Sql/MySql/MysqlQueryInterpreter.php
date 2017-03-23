<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 3:45 PM
 */

namespace Sm\Storage\Modules\Sql\MySql;


use Sm\Abstraction\Identifier\Identifier;
use Sm\Entity\EntityType;
use Sm\Entity\Property\Property;
use Sm\Entity\Property\PropertyContainer;
use Sm\Entity\Property\PropertyHaver;
use Sm\Query\Query;
use Sm\Storage\Modules\Sql\Formatter\FromFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyAsColumnFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SelectFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceFragment;
use Sm\Storage\Modules\Sql\Formatter\WhereFragment;
use Sm\Storage\Modules\Sql\Interpreter\SqlQueryInterpreter;

/**
 * Class MysqlQueryInterpreter
 *
 * Meant to handle the execution and interpretation of Mysql Queries
 *
 * @package Sm\Storage\Modules\Sql\MySql
 */
class MysqlQueryInterpreter extends SqlQueryInterpreter {
    /**
     * @param array              $owners_by_property
     * @param PropertyFragment[] $PropertyFragment_array
     * @param array              $row
     *
     * @return array
     */
    public function createSelectResultObject($owners_by_property, $PropertyFragment_array, array $row) {
        $return_value = [];
        foreach ($PropertyFragment_array as $PropertyFragment) {
            $Property           = $PropertyFragment->getProperty();
            $name               = $Property->name;
            $propertyString     = "{$Property}";
            $property_object_id = $Property->getObjectId();
            
            $alias  = str_replace($propertyString,
                                  $name,
                                  $PropertyFragment->getAlias());
            $owners = $owners_by_property[ $property_object_id ] ?? null;
            $owners = $owners ? (is_array($owners) ? $owners : [ $owners ]) : null;
            
            foreach ($owners as $owner_id) {
                $Owner = Identifier::identify($owner_id);
                $value = $row[ $alias ] ?? null;
                if ($Owner instanceof PropertyHaver) {
                    $OwnerProperty = $Owner->{$Property->name} = clone $Property;
                    $OwnerProperty->setValue($value);
                }
                $return_value[ $owner_id ]                    = $return_value[ $owner_id ] ?? [];
                $return_value[ $owner_id ][ $Property->name ] = $value;
            }
        }
        return $return_value;
    }
    
    /**
     * From the Select array (the array that comes from the Query::getSelectArray()), get all properties in a list.
     *
     * @param $SelectArray
     *
     * @return array
     */
    protected function getAllProperties($SelectArray) {
        $Properties = [];
        # Iterate through the properties and PropertyContainers to get the
        foreach ($SelectArray as $PropertyOrContainer) {
            if ($PropertyOrContainer instanceof Property) {
                $Properties[] = $PropertyOrContainer;
            } else if ($PropertyOrContainer instanceof PropertyContainer) {
                foreach ($PropertyOrContainer as $property) {
                    $Properties[] = $property;
                }
            }
        }
        return $Properties;
    }
    /**
     * Get an array, indexed by property object_id, that has all of the Properties of that
     *
     * @param $OwnersByPropertyId
     *
     * @return array
     *
     */
    protected function getOwnersByProperty($OwnersByPropertyId) {
        $OwnersByProperty = [];
        /**
         * @var string   $owner_id
         * @var Property $Property
         */
        foreach ($OwnersByPropertyId as $owner_id => $Properties) {
            foreach ($Properties as $Property) {
                $OwnersByProperty[ $Property->getObjectId() ] = $owner_id;
            }
        }
        return $OwnersByProperty;
    }
    /**
     * Execute a Select Query.
     *
     * @param \Sm\Query\Query $Query
     *
     * @return array
     */
    protected function interpret_select(Query $Query) {
        $SqlModule = $this->SqlModule;
        
        $Select          = $Query->getSelectArray();
        $select_owners   = $this->getAllSelectOwners($this->getAllProperties($Select));
        $SelectFragment  = $this->createSelectFragment($Query, $select_owners);
        $SelectStatement = $SqlModule->format($SelectFragment);
        foreach ($SqlModule->FormatterFactory->Aliases as $alias => $item) $SelectStatement = str_replace($alias, $SqlModule->format($item), $SelectStatement);
        foreach ($SqlModule->FormatterFactory->Aliases as $alias => $item) $SelectStatement = str_replace($alias, $SqlModule->format($item), $SelectStatement);
        echo "{$SelectStatement}\n\n";
        
        
        $results            = [];
        $rows               = $this->executeSelectStatement($SelectStatement);
        $owners_by_property = $this->getOwnersByProperty($select_owners);
        foreach ($rows as $row) $results[] = $this->createSelectResultObject($owners_by_property,
                                                                             $SelectFragment->getProperties(),
                                                                             $row);
        return $results;
    }
    /**
     * Execute the Select Statement
     *
     * @param $SelectStatement
     *
     * @return \PDOStatement
     */
    protected function executeSelectStatement($SelectStatement): \PDOStatement {
        $SqlModule      = $this->SqlModule;
        $DatabaseSource = $SqlModule->getDatabaseSource();
        /** @var \PDO $Connection */
        $Connection = $DatabaseSource->getConnection();
        $result     = $Connection->query("$SelectStatement", \PDO::FETCH_ASSOC);
        
        return $result;
    }
    /**
     * Iterate through the Properties or PropertyContainers, separating out what we have. This gives us a consistent way
     * to refer to the things that we are querying.
     *
     * @param $properties_by_owner_array
     *
     * @return array|\Sm\Storage\Modules\Sql\Formatter\SelectFragment
     */
    protected function createSelectFragment(Query $Query, $properties_by_owner_array) {
        /** @var array $source_aliases An array, indexed by owner_id, that contains the name of the tables being aliased. */
        $source_aliases    = [];
        $property_aliases  = [];
        $PropertyFragments = [];
        $count             = 0;
        /**
         * @var string                       $owner_id
         * @var \Sm\Entity\Property\Property $Property
         */
        foreach ($properties_by_owner_array as $owner_id => $property_array) {
            # The table alias is what we use to refer to a the same table.
            #   This is useful in situations when the table is being used to mean multiple different things.
            #   In cases such as "SELECT sections.*, sections_2.* FROM sections, sections s
            foreach ($property_array as $Property) {
                $_PropertyFragment                                    = $this->createPropertyFragment($Property, $owner_id, $count);
                $_SourceFragment                                      = $_PropertyFragment->getSourceFragment();
                $PropertyFragments[ $Property->getObjectId() ]        = $_PropertyFragment;
                $source_aliases[ $_SourceFragment->getSourceAlias() ] = $_SourceFragment->getSource();
            }
            $Owner = Identifier::identify($owner_id);
            if ($Owner instanceof EntityType) {
                $IC = $Owner->getIdentifyingCondition(static::class);
                if ($IC) $Query->where($IC);
            }
        }
        $WhereFragment = $this->createWhereFragment($Query);
        $FromFragment  = FromFragment::init()->setAliases($source_aliases);
        $this->SqlModule->FormatterFactory->Aliases->register($source_aliases);
        return SelectFragment::init()
                             ->setFrom($FromFragment)
                             ->setPropertyFragments($PropertyFragments)
                             ->setWhere($WhereFragment);
    }
    /**
     * Create a PropertyFragment from a Property
     *
     * @param $Property
     * @param $count
     * @param $owner_id
     *
     * @return PropertyFragment
     */
    protected function createPropertyFragment(Property $Property, $owner_id, &$count = 0) {
        $TableSource     = $Property->getSource();
        $table_alias     = $count++ ? $TableSource->getName() : $this->SqlModule->format($owner_id);
        $table_alias     = "{$table_alias}";
        $_property_alias = self::getPropertyAlias($Property, $table_alias);
        
        $_PropertyFragment = PropertyAsColumnFragment::init()
                                                     ->setAlias($_property_alias)
                                                     ->setSourceFragment(SourceFragment::init()
                                                                                       ->setSource($TableSource)
                                                                                       ->setSourceAlias($table_alias))
                                                     ->setProperty($Property);
        
        $this->SqlModule->FormatterFactory->addFragment($Property, $_PropertyFragment);
        $this->SqlModule->FormatterFactory->Aliases->register($Property->object_id, $Property->name);
        
        return $_PropertyFragment;
    }
    protected function createWhereFragment(Query $Query) {
        $WhereArray = $Query->getWhere();
        return WhereFragment::init()->setWhere($WhereArray);
    }
    /**
     * Get the alias that should be used to refer to a property.
     *
     * @param \Sm\Entity\Property\Property $Property    The property that we are going to add.
     * @param string                       $table_alias The alias of the table that we are going to add it to.
     *
     * @return string
     */
    protected static function getPropertyAlias($Property, $table_alias) {
        return "{$table_alias}_{$Property}";
    }
}