<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 10:23 PM
 */

namespace Sm\Data\Datatype\Variable_;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\Internal\Identification\Identifier;
use Sm\Core\Resolvable\Error\UnresolvableError;
use Sm\Core\Resolvable\NullResolvable;
use Sm\Core\Resolvable\Resolvable;
use Sm\Core\Resolvable\ResolvableFactory;

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
class Variable_ extends Resolvable implements Identifiable, \JsonSerializable {
    use HasObjectIdentityTrait;
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
        $this->setObjectId(Identifier::generateIdentity($this));
        parent::__construct($subject);
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
     * Set the value of this Variable. Subject must be
     *
     * @param Resolvable $subject
     *
     * @return $this
     * @throws \Sm\Core\Resolvable\Error\UnresolvableError
     */
    public function setSubject($subject) {
        if ($subject === null) {
            return $this;
        }
        # Only deal with ResolvablesL
        
        $subject = $this->getFactoryContainer()->resolve(ResolvableFactory::class)->build($subject);
        
        if (!($subject instanceof Resolvable)) {
            throw new UnresolvableError("Cannot set Subject to be something that is not resolvable");
        }
        
        $class = get_class($subject);
        
        # If we haven't given permission to set a Resolvable of this type, don't
        if ($len = count($this->_potential_types)) {
            # iterate through the potential types and see if we're allowed to continue;
            for ($i = 0; $i < $len; $i++) {
                if ($class === $this->_potential_types[ $i ] || is_subclass_of($class, $this->_potential_types[ $i ])) {
                    break;
                } else if ($i === $len - 1) {
                    throw new UnresolvableError("Cannot set this class");
                }
            }
        }
        
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
     * Return the Value of the Variable or just return the Variable
     *
     * @return $this|mixed
     */
    public function resolve($_ = null) {
        if (isset($this->subject)) {
            return $this->subject->resolve();
        }
        return null;
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
    public function setDefault($default): Variable_ {
        $this->_default = $default instanceof Resolvable ? $default : $this->getFactoryContainer()
                                                                           ->resolve(ResolvableFactory::class)
                                                                           ->build($default);
        return $this;
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
    public static function _($name) {
        return static::init($name);
    }
}