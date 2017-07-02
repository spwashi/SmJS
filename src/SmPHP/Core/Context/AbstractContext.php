<?php
/**
 * User: Sam Washington
 * Date: 6/26/17
 * Time: 10:12 PM
 */

namespace Sm\Core\Context;


use Sm\Core\Container\Container;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;

/**
 * Class AbstractContext
 *
 * The general stuff that most Contexts will have in common
 *
 * @package Sm\Core\Context
 *
 */
abstract class AbstractContext implements Context {
    use HasObjectIdentityTrait;
    /**
     * @var  \Sm\Core\Container\Container A Container for the Attributes of this Context that make it special
     *                                    They are key identifiers of this context
     */
    protected $items;
    /** @var array An array of strings that identify the core components of this Context */
    protected $expected_items = [];
    
    /**
     * AbstractContext constructor.
     *
     */
    public function __construct() {
        $this->items = Container::init();
        $this->createSelfID();
        $this->incorporate('context_id', $this->getObjectId());
    }
    
    public function __get($name) {
        $item = $this->items->resolve($name);
//        if (!$item) throw new UnimplementedError("Have not yey thought about what to do in this situation");
        return $item;
    }
    /**
     * @inheritdoc
     *
     * @return null|\Sm\Core\Container\Container
     */
    public function readContextAttributes(): ?Container {
        //todo readonly?
        return $this->items;
    }
    /**
     * Method to identify an item on this Context as being part of the Context.
     *
     * @param $name
     * @param $item
     *
     * @return mixed
     */
    protected function incorporate($name, $item) {
        $this->items->register($name, $item);
        return $this;
    }
    
}