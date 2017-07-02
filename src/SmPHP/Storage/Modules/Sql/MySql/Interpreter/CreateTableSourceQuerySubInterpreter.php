<?php
/**
 * User: Sam Washington
 * Date: 4/1/17
 * Time: 8:46 PM
 */

namespace Sm\Storage\Modules\Sql\MySql\Interpreter;


use Sm\Core\Exception\Exception;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Data\Datatype\Integer_;
use Sm\Data\Datatype\Null_;
use Sm\Data\Datatype\String_;
use Sm\Storage\Modules\Sql\Formatter\ColumnAsDefinitionFragment;
use Sm\Storage\Modules\Sql\Formatter\CreateTableFragment;
use Sm\Storage\Modules\Sql\Formatter\PropertyFragment;
use Sm\Storage\Modules\Sql\Formatter\SourceFragment;

/**
 * Class CreateStatementSubInterpreter
 *
 * Meant to handle the execution of Create statements
 *
 * @package Sm\Storage\Modules\Sql\MySql\Interpreter
 */
class CreateTableSourceQuerySubInterpreter extends MysqlQuerySubInterpreter {
    /**
     *
     */
    public function execute() {
        $statement = $this->createStatement();
        /** @var \Sm\Storage\Database\TableSource $Item */
        $Item           = $this->Query->create_item;
        $DatabaseSource = $Item->getRootSource();
        echo "{$statement}\n\n--------------------------\n\n";
        /** @var \Sm\Storage\Modules\Sql\MySql\MysqlDatabaseSource $DatabaseSource */
        $sth    = $DatabaseSource->getConnection()->prepare("$statement");
        $result = $sth->execute();
        
        return $result;
    }
    
    
    /**
     * Complete the QueryInterpreter, returning a string that represents the Query to execute
     *
     * @return string
     */
    public function createFragment() {
        $Fragment = CreateTableFragment::init()
            ->setSourceFragment($this->createTableSourceFragment())
            ->setColumnFragmentArray($this->createColumnFragmentArray());
        
        return $Fragment;
    }
    protected function createTableSourceFragment() {
        $SourceFragment = SourceFragment::init()->setSource($this->Query->create_item);
        return $SourceFragment;
    }
    protected function getQueryProperties() {
        /** @var \Sm\Storage\Database\TableSource $Source */
        $Source = $this->Query->create_item;
        return $Source->Columns->getAll();
    }
    /**
     * Create a Column Fragment based on the columns
     *
     * @param \Sm\Data\Property\Property           $Column
     * @param \Sm\Storage\Database\ColumnContainer $Columns
     *
     * @return ColumnAsDefinitionFragment
     * @throws \Sm\Core\Exception\Exception
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    private function createColumnFragment($Column, $Columns) {
        $name            = $Column->name;
        $potential_types = $Column->potential_types;
        # region Can Be Null
        $_index_of_null_type = array_search(Null_::class, $potential_types);
        $can_be_null         = $_index_of_null_type !== false;
        
        # remove null from the list of types
        if ($can_be_null) {
            array_splice($potential_types, $_index_of_null_type, 1);
        }
        
        # endregion
        
        # region Data type
        if (($num_types = count($potential_types)) !== 1) {
            throw new Exception("Not sure how to determine the column type when there are {$num_types} in {$name}");
        }
        
        $datatype_string = $potential_types[0] ?? null;
        
        switch ($datatype_string) {
            case String_::class:
                $max_length      = $Column->getMaxLength() ?? 11;
                $datatype_string = "VARCHAR({$max_length})";
                break;
            case Integer_::class:
                $max_length      = $Column->getMaxLength() ?? 11;
                $datatype_string = "INTEGER({$max_length})";
                break;
            default:
                throw new UnimplementedError("Not sure how to create column of type {$datatype_string}");
        }
        
        # endregion
        
        $DefaultResolvable = $Column->default;
        if (isset($DefaultResolvable) && ($DefaultResolvable instanceof NullResolvable)) {
            $can_be_null = true;
        }
        
        
        $_is_primary_key = $Columns->isPrimarykey($Column);
        
        /** @var ColumnAsDefinitionFragment $ColumnFragment */
        
        if (($Reference = $Column->reference) && ($Reference !== $Column)) {
            
            # todo we should probably check to see if the sources are the same
            
            $_ReferenceRootSource = $Reference->getSource()->getRootSource();
            $_ColumnRootSource    = $Column->getSource()->getRootSource();
            if ($_ReferenceRootSource !== $_ColumnRootSource) {
                throw new UnimplementedError("Cannot yet query across sources");
            }
            
            
            $ReferenceColumnFragment = $this->createColumnFragment($Reference, $Columns);
            # Used for Foreign Keys
            if ($ReferenceColumnFragment) {
                $ColumnFragment = ColumnAsDefinitionFragment::inherit($ReferenceColumnFragment);
                
                # Mainly because Properties and columns are separate things for whatever reasons
                $ColumnFragment->setReferenceFragment(PropertyFragment::inherit($ReferenceColumnFragment)
                                                          ->setSourceFragment(SourceFragment::init()
                                                                                  ->setSource($Reference->getSource())));
            }
        }
        
        if (!isset($ColumnFragment)) {
            $ColumnFragment = ColumnAsDefinitionFragment::init();
        }
        
        $ColumnFragment->setProperty($Column);
        $ColumnFragment->setCanBeNull($can_be_null);
        
        # If this doesn't already have an associated data type
        if (null === $ColumnFragment->getDataType()) {
            $ColumnFragment->setDataType($datatype_string);
        }
        
        $ColumnFragment->setIsPrimaryKey($_is_primary_key);
        
        if (isset($DefaultResolvable)) {
            $ColumnFragment->setDefaultValue($DefaultResolvable->resolve());
        }
        return $ColumnFragment;
    }
    private function createColumnFragmentArray() {
        /** @var \Sm\Storage\Database\TableSource $Source */
        $Source              = $this->Query->create_item;
        $Columns             = $Source->Columns;
        $ColumnFragmentArray = [];
        
        /**
         * @var                              $name
         * @var \Sm\Data\Property\Property   $Column
         */
        foreach ($Columns as $name => $Column) {
            $ColumnFragment        = $this->createColumnFragment($Column, $Columns);
            $ColumnFragmentArray[] = $ColumnFragment;
        }
        return $ColumnFragmentArray;
    }
}