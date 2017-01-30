<?php
/**
 * User: spwashi2
 * Date: 1/25/2017
 * Time: 11:27 AM
 */

namespace Sm\App;


use Sm\Abstraction\Registry;

class App implements Registry {
    protected $AppConfig = null;
    
    #  Constructors/Initializers
    #-----------------------------------------------------------------------------------
    
    public function __construct(Registry $AppConfig) {
        $this->AppConfig = $this->normalizeAppConfig($AppConfig);
    }
    public static function init(Registry $AppConfig = null) {
        return new static($AppConfig);
    }
    
    #  Getters and Setters
    #-----------------------------------------------------------------------------------
    
    public function __get($name) {
        return $this->resolve($name);
    }
    public function register($identifier, $registrand = null) {
        if (is_array($identifier)) {
            foreach ($identifier as $key => $value) {
                $this->AppConfig ? $this->AppConfig->register($identifier, $registrand) : null;
            }
        } else if (!property_exists($this, $identifier)) {
            $this->AppConfig ? $this->AppConfig->register($identifier, $registrand) : null;
        } else {
            $this->$identifier = $registrand;
        }
        return $this;
    }
    
    public function resolve($identifier, $arguments = null) {
        $arguments_class_exists = class_exists('\Sm\Abstraction\Resolvable\Arguments');
        
        if (!$arguments_class_exists || !($arguments instanceof \Sm\Abstraction\Resolvable\Arguments)) {
            $arguments = func_get_args();
            $arguments = array_shift($arguments);
            if (class_exists('\Sm\Abstraction\Resolvable\Arguments')) {
                $arguments = \Sm\Abstraction\Resolvable\Arguments::coerce($arguments);
            }
        }
        
        if (isset($this->AppConfig)) return $this->AppConfig->resolve($identifier, $arguments);
        return $this->$identifier ?? null;
    }
    
    #  Protected/private methods
    #-----------------------------------------------------------------------------------
    
    /**
     * Normalize the way that this App is being configured
     *
     * @param $AppConfig
     *
     * @return Registry
     */
    protected function normalizeAppConfig($AppConfig) {
        if (class_exists('\Sm\IoC\IoC') && !isset($AppConfig)) $AppConfig = \Sm\IoC\IoC::init();
        
        return $AppConfig;
    }
}