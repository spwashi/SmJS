<?php
/**
 * User: Sam Washington
 * Date: 3/5/17
 * Time: 3:40 PM
 */

namespace Sm\Formatter;


use Sm\Abstraction\Formatting\Formatter;
use Sm\Abstraction\Identifier\Identifiable;
use Sm\Container\Container;
use Sm\Factory\Factory;

/**
 * Class FormatterFactory
 *
 * @property null|\Sm\Container\Container Aliases
 * @package Sm\Formatter
 */
class FormatterFactory extends Factory {
    /**
     * This is a Container, indexed by object_id, that contains fragments created from objects
     *
     * @var \Sm\Container\Container
     */
    protected $Fragments;
    protected $Aliases;
    /**
     * FormatterFactory constructor.
     */
    public function __construct() {
        $this->Fragments = new Container;
        $this->Aliases   = new Container;
    }
    public function __get($name) {
        if ($name === 'Aliases') return $this->Aliases;
        return null;
    }
    
    /**
     * Add a Fragment to the registry. This allows us to maintain consistent representations of things in the FormatterFactory
     *
     * @param \Sm\Abstraction\Identifier\Identifiable $item
     * @param                                         $Fragment
     *
     * @return $this
     */
    public function addFragment(Identifiable $item, $Fragment) {
        $name = get_class($Fragment) . '|' . $item->getObjectId();
        $this->Fragments->register($name, $Fragment);
        return $this;
    }
    /**
     * @param $item
     * @param $fragment_type
     *
     * @return array<string>
     */
    public function checkOutFragment($item, $fragment_type) {
        $name = $this->getFragmentName($item, $fragment_type);
        $item = $this->getFragment($item, $fragment_type);
        $this->Fragments->register($name, null);
        return $item;
    }
    public function getFragment(Identifiable $item, $fragment_type) {
        $name = $this->getFragmentName($item, $fragment_type);
        return $this->Fragments->resolve($name);
    }
    public function canCreateClass($object_type) {
        return is_a($object_type, Formatter::class, true);
    }
    /**
     * @param null $item
     *
     * @return null
     */
    public function build($item = null) {
        $result = parent::build($item, $this);
        return PlainStringFormatter::coerce($result);
    }
    public function format($item) {
        if (is_array($item)) {
            $formatted_item = [];
            foreach ($item as $index => $value) {
                $formatted_item[ $index ] = $this->build($value);
            }
            return $formatted_item;
        }
        return $this->build($item);
    }
    protected function getFragmentName(Identifiable $item, $fragment_type) {
        return "{$fragment_type}|{$item->getObjectId()}";
    }
}