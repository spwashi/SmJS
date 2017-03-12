<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 11:01 AM
 */

namespace Sm\Query\Sql;


use Sm\App\Module\Module;
use Sm\Formatter\FormatterFactory;
use Sm\Formatter\FormatterFactoryHaver;
use Sm\Resolvable\Error\UnresolvableError;

/**
 * Class SqlModule
 *
 * @property-read FormatterFactory FormatterFactory
 *
 * @package Sm\Query\Sql
 */
class SqlModule extends Module {
    /** @var  FormatterFactory $FormatterFactory */
    protected $FormatterFactory;
    
    /**
     * Getter provides access to some of the module properties
     *
     * @param $name
     *
     * @return null
     */
    public function __get($name) {
        if (isset($this->$name)) return $this->$name;
        return null;
    }
    
    /**
     * Dispatch the SqlModule, return $this.
     * All of the important stuff should be attached to this Module directly, not based on the result of the dispatch.
     *
     * @return $this
     */
    public function dispatch() {
        parent::dispatch($this);
        return $this;
    }
    public function format(...$args) {
        foreach ($args as $item) {
            /** @var FormatterFactoryHaver $item */
            if (!($item instanceof FormatterFactoryHaver)) continue;
            $item->setFormatterFactory($this->FormatterFactory);
        }
        return $this->dispatch()->FormatterFactory->build(...$args);
    }
    /**
     * @param FormatterFactory $FormatterFactory
     *
     * @return SqlModule
     */
    public function setFormatterFactory(FormatterFactory $FormatterFactory): SqlModule {
        $this->FormatterFactory = $FormatterFactory;
        return $this;
    }
    protected function assertComplete() {
        if (!isset($this->FormatterFactory) || !($this->FormatterFactory instanceof FormatterFactory)) {
            throw new UnresolvableError("There needs to be a formatter factory in order for this Module to operate correctly.");
        }
    }
}