<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 11:36 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Entity\Source\DataSource;

class SourceFragment extends SqlFragment {
    public    $Source;
    public    $source_alias;
    protected $PropertyHaver_object_id;
    
    /**
     * @return \Sm\Entity\Source\DataSource
     */
    public function getSource() {
        return $this->Source;
    }
    /**
     * @param mixed $Source
     *
     * @return $this
     */
    public function setSource(DataSource $Source) {
        $this->Source = $Source;
        return $this;
    }
    public function getPropertyHaverObjectId() {
        return $this->PropertyHaver_object_id;
    }
    /**
     * Set the object_id of the PropertyHaver of this DataSource in case we're doing an "alias by PropertyHaver/source" kinda thing
     *
     * @param mixed $PropertyHaver_object_id
     *
     * @return $this
     */
    public function setPropertyHaverObjectId($PropertyHaver_object_id) {
        $this->PropertyHaver_object_id = $PropertyHaver_object_id;
        return $this;
    }
}