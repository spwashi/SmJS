<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 11:36 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Source\Source;

class SourceFragment extends SqlFragment {
    public    $Source;
    public    $source_alias;
    protected $owner_object_id;
    
    /**
     * Return the variables that this class deems relevant to the formatter.
     *
     * @return array
     */
    public function getVariables(): array {
        return [ 'Source' => $this->Source, 'owner_object_id' => $this->owner_object_id ];
    }
    /**
     * @return \Sm\Storage\Source\Source
     */
    public function getSource() {
        return $this->Source;
    }
    /**
     * @param mixed $Source
     *
     * @return $this
     */
    public function setSource(Source $Source) {
        $this->Source = $Source;
        return $this;
    }
    public function getOwnerObjectId() {
        return $this->owner_object_id;
    }
    /**
     * Set the object_id of the Owner of this Source in case we're doing an "alias by owner/source" kinda thing
     *
     * @param mixed $owner_object_id
     *
     * @return $this
     */
    public function setOwnerObjectId($owner_object_id) {
        $this->owner_object_id = $owner_object_id;
        return $this;
    }
}