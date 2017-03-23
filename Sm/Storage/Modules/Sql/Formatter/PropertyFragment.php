<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 9:04 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


/**
 * Class PropertyFragment
 *
 * @package Sm\Storage\Modules\Sql\Formatter
 */
class PropertyFragment extends SqlFragment {
    protected $Property;
    protected $SourceFragment;
    protected $alias;
    
    public function getVariables(): array {
        return [
            'Property'       => $this->Property,
            'alias'          => $this->alias,
            'SourceFragment' => $this->SourceFragment,
        ];
    }
    public function getAlias() {
        return $this->alias;
    }
    /**
     * @param mixed $alias
     *
     * @return PropertyFragment
     */
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }
    /**
     * @return \Sm\Entity\Property\Property
     */
    public function getProperty() {
        return $this->Property;
    }
    /**
     * @param mixed $Property
     *
     * @return PropertyFragment
     */
    public function setProperty($Property) {
        $this->Property = $Property;
        return $this;
    }
    /**
     * @return \Sm\Storage\Modules\Sql\Formatter\SourceFragment
     */
    public function getSourceFragment() {
        return $this->SourceFragment;
    }
    /**
     * @param \Sm\Storage\Modules\Sql\Formatter\SourceFragment $SourceFragment
     *
     * @return $this
     */
    public function setSourceFragment(SourceFragment $SourceFragment) {
        $this->SourceFragment = $SourceFragment;
        return $this;
    }
}