<?php
/**
 * User: Sam Washington
 * Date: 4/16/17
 * Time: 11:46 PM
 */

namespace Sm\Storage\Modules\Sql\Formatter;


use Sm\Storage\Modules\Sql\Formatter\Traits\FragmentHasPropertyFragmentTrait;

class ColumnAsDefinitionFragment extends SqlFragment {
    use FragmentHasPropertyFragmentTrait;
    protected $max_length;
    protected $data_type;
    protected $default_value;
    protected $has_default_value = false;
    protected $can_be_null;
    /** @var string|bool $is_primary_key */
    protected $is_primary_key;
    /** @var  PropertyFragment $reference_fragment If this Fragment is a foreign key to another one, this is it */
    protected $reference_fragment;
    /**
     * @return bool
     */
    public function canBeNull(): bool {
        return $this->can_be_null;
    }
    /**
     * @return mixed
     */
    public function getDataType() {
        return $this->data_type;
    }
    /**
     * @param mixed $data_type
     *
     * @return ColumnAsDefinitionFragment
     */
    public function setDataType($data_type) {
        $this->data_type = $data_type;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getMaxLength() {
        return $this->max_length;
    }
    /**
     * @param mixed $max_length
     *
     * @return ColumnAsDefinitionFragment
     */
    public function setMaxLength($max_length) {
        $this->max_length = $max_length;
        return $this;
    }
    /**
     * @param bool $can_be_null
     *
     * @return $this
     */
    public function setCanBeNull(bool $can_be_null) {
        $this->can_be_null = $can_be_null;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->default_value;
    }
    /**
     * @param mixed $default_value
     *
     * @return ColumnAsDefinitionFragment
     */
    public function setDefaultValue($default_value) {
        $this->default_value     = $default_value;
        $this->has_default_value = true;
        return $this;
    }
    /**
     * @return bool
     */
    public function hasDefaultValue(): bool {
        return $this->has_default_value;
    }
    /**
     * Is this column a primary key?
     *
     * @param bool|string $is_primary_key
     *
     * @return ColumnAsDefinitionFragment
     */
    public function setIsPrimaryKey($is_primary_key) {
        $this->is_primary_key = $is_primary_key;
        return $this;
    }
    /**
     * @return bool|string
     */
    public function isPrimaryKey() {
        return $this->is_primary_key;
    }
    /**
     * @return PropertyFragment|null
     */
    public function getReferenceFragment() {
        return $this->reference_fragment;
    }
    /**
     * @param PropertyFragment $reference_fragment
     *
     * @return ColumnAsDefinitionFragment
     */
    public function setReferenceFragment(PropertyFragment $reference_fragment) {
        $this->reference_fragment = $reference_fragment;
        return $this;
    }
}