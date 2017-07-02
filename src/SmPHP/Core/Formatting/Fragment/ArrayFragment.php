<?php
/**
 * User: Sam Washington
 * Date: 4/15/17
 * Time: 4:58 PM
 */

namespace Sm\Core\Formatting\Fragment;


class ArrayFragment extends Fragment {
    protected $array = [];
    
    /**
     * Return the variables that this class deems relevant to the formatter.
     *
     * @return array
     */
    public function getFormattedAttributes(): array {
        return [
            'array' => $this->array,
        ];
    }
    /**
     * @return array
     */
    public function getArray(): array {
        return $this->array;
    }
    /**
     * @param array $array
     *
     * @return $this
     */
    public function setArray(array $array) {
        $this->array = $array;
        return $this;
    }
}