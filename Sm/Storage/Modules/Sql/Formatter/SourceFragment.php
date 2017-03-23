<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 11:36 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Source\Source;

class SourceFragment extends SqlFragment {
    public $Source;
    public $source_alias;
    /**
     * Return the variables that this class deems relevant to the formatter.
     *
     * @return array
     */
    public function getVariables(): array {
        return [ 'source_alias' => $this->source_alias, 'Source' => $this->Source ];
    }
    /**
     * @return mixed
     */
    public function getSourceAlias() {
        return $this->source_alias;
    }
    /**
     * Set the alias of the Source
     *
     * @param string $source_alias
     *
     * @return $this
     */
    public function setSourceAlias($source_alias) {
        $this->source_alias = $source_alias;
        return $this;
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
}