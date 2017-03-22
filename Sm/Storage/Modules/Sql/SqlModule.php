<?php
/**
 * User: Sam Washington
 * Date: 3/11/17
 * Time: 11:01 AM
 */

namespace Sm\Storage\Modules\Sql;


use Sm\App\Module\Module;
use Sm\Container\Container;
use Sm\Formatter\FormatterFactory;
use Sm\Formatter\FormatterFactoryHaver;
use Sm\Resolvable\Error\UnresolvableError;
use Sm\Storage\Source\Database\DatabaseSource;

/**
 * Class SqlModule
 *
 * @property-read FormatterFactory                                FormatterFactory
 * @property-read \Sm\Storage\Source\Database\DatabaseSource|null $DatabaseSource
 *
 * @package Sm\Query\Sql
 */
class SqlModule extends Module {
    /** @var  FormatterFactory $FormatterFactory */
    protected $FormatterFactory;
    /** @var  \Sm\Container\Container $DatabaseSourceContainer */
    protected $DatabaseSourceContainer;
    /**
     * SqlModule constructor.
     *
     * @param mixed|null $subject
     */
    public function __construct($subject = null) {
        parent::__construct($subject);
        $this->DatabaseSourceContainer = new Container;
    }
    
    
    /**
     * Getter provides access to some of the module properties
     *
     * @param $name
     *
     * @return null
     */
    public function __get($name) {
        if (isset($this->$name)) return $this->$name;
        if ($name === 'DatabaseSource') return $this->getDatabaseSource();
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
    /**
     * Get the DatabaseSource that is going to be used
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getDatabaseSource($name = 'default') {
        return $this->DatabaseSourceContainer->resolve($name);
    }
    /**
     * Add a DatabaseSource to the list of those registered for this SqlModule
     *
     * @param        $DatabaseSource
     * @param string $name
     *
     * @return $this
     */
    public function registerDatabaseSource($DatabaseSource, $name = 'default') {
        $this->DatabaseSourceContainer->register($name, $DatabaseSource);
        return $this;
    }
    protected function assertComplete() {
        if (!isset($this->FormatterFactory) || !($this->FormatterFactory instanceof FormatterFactory)) {
            throw new UnresolvableError("There needs to be a formatter factory in order for this Module to operate correctly.");
        }
    
        if (!($this->DatabaseSource instanceof DatabaseSource)) {
            throw new UnresolvableError("There must be a DatabaseSource in order for this SqlModule to work as predicted");
        }
    }
}