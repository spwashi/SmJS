<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 10:23 PM
 */

namespace Sm\Data\Type\Variable_;


use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Core\Util;
use Sm\Data\Type\Variable_\Exception\InvalidVariableTypeError;

/**
 * Class Variable_
 *
 * @property-read mixed $default_value      The Resolvable that holds the value of the Variable_
 * @property-read mixed $default            The default of the Variable_
 * @property string     $name               The name of the Variable_
 * @property mixed      $value              The resolved value of this Variable_'s subject
 * @property Resolvable $raw_value          The raw, unresolved Resolvable that this Variable_ holds a reference to
 *
 * @package Sm\Type\Variable_
 */
class Variable_ extends AbstractResolvable implements \JsonSerializable {
    /** @var  Resolvable $subject */
    protected $subject;
    /** @var  Resolvable $_default */
    protected $_default;
    protected $_name;
    protected $_potential_types = [];
    /**
     * Variable_ constructor.
     *
     * @param mixed|null $subject
     */
    public function __construct($subject = null) {
        $subject = $subject ?? NullResolvable::init();
        parent::__construct($subject);
    }
    /**
     * Create a Variable
     *
     * @param string|null $name The name of the Variable
     *
     * @return static
     */
    public static function init($name = null) {
        $Instance       = new static;
        $Instance->name = $name;
        return $Instance;
    }
    
    public function __get($name) {
        if ($name === 'name') {
            return $this->_name;
        }
        if ($name === 'value') {
            return $this->resolve();
        }
        if ($name === 'raw_value') {
            return $this->subject;
        }
        if ($name === 'default_value') {
            return $this->_default->resolve();
        }
        if ($name === 'default') {
            return $this->_default;
        }
        return null;
    }
    /**
     * Make some things a little bit more convenient
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        switch ($name) {
            case 'name':
                $this->_name = $value;
                break;
            case 'value':
                $this->setValue($value);
                break;
            case 'default':
                $this->setDefault($value);
                break;
        }
    }
    
    /**
     * Get an array of the potential types that this Variable can be
     *
     * @return array
     */
    public function getPotentialTypes(): array {
        return $this->_potential_types;
    }
    /**
     * Set an array of the potential types that a the value can be
     *
     * @param $_potential_types
     *
     * @return $this
     */
    public function setPotentialTypes(string ...$_potential_types) {
        $this->_potential_types = $_potential_types;
        return $this;
    }
    
    /**
     * Set the value of this Variable. Subject must be of the type specified.
     *
     * @param Resolvable $subject
     *
     * @return $this
     * @throws \Sm\Data\Type\Variable_\Exception\InvalidVariableTypeError
     */
    public function setSubject($subject) {
        # todo check CanBeNull
        if ($subject === null) {
            return $this;
        }
    
        # Only deal with Resolvables
        $subject = (new ResolvableFactory)->resolve($subject);
    
        $this->checkCanSetValue($subject);
        
        parent::setSubject($subject);
        return $this;
    }
    /**
     * Alias for "set subject"
     *
     * @param $value
     *
     * @return $this
     */
    public function setValue($value) {
        return $this->setSubject($value);
    }
    
    /**
     * Return the Value of this subject or null if the subject doesn't exist
     *
     * @param null $_
     *
     * @return Resolvable
     */
    public function resolve($_ = null) {
        return $this->subject ? $this->subject->resolve() : null;
    }
    
    /**
     * Does this Variable have a value to resolve to?
     *
     * @return bool
     */
    public function canResolve() {
        return isset($this->subject);
    }
    function jsonSerialize() {
        return [ 'name' => $this->_name, '_type' => Variable_::class ];
    }
    /**
     * Set the Default Value
     *
     * @param Resolvable|mixed $default
     *
     * @return Variable_
     */
    public function setDefault(Resolvable $default): Variable_ {
        $this->_default = $default;
        return $this;
    }
    public static function _($name) {
        return static::init($name);
    }
    /**
     * @param $subject
     *
     * @throws \Sm\Data\Type\Variable_\Exception\InvalidVariableTypeError
     */
    protected function checkCanSetValue($subject) {
        
        # If we haven't given permission to set a Resolvable of this type, don't
        $potential_types = $this->_potential_types;
    
        if (!Util::isOneOfListedTypes($subject, $potential_types)) {
            throw new InvalidVariableTypeError("Cannot set subject to be this value");
        }
    }
}