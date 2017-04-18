<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 8:30 PM
 */

namespace Sm\Entity\Property;


use Sm\Abstraction\ReadonlyTrait;
use Sm\Entity\EntityTypeVariable;
use Sm\Error\Error;
use Sm\Error\UnimplementedError;
use Sm\Resolvable\NullResolvable;
use Sm\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;
use Sm\Resolvable\ResolvableResolvable;
use Sm\Storage\Source\NullSource;
use Sm\Storage\Source\Source;
use Sm\Storage\Source\SourceHaver;
use Sm\Type\Variable_\Variable_;

/**
 * Class Property
 *
 * Represents a property held by an Entity or Model
 *
 * @mixin ReadonlyTrait
 * @mixin Variable_
 *
 * @package Sm\Entity\Property
 *
 * @property-read string                             $name
 * @property-read \Sm\Entity\Property\Property|false $reference
 * @property-read string                             $object_id
 * @property-read array                              $potential_types
 */
class Property extends Variable_ implements SourceHaver {
    use ReadonlyTrait;
    
    /** @var  string $name */
    protected $_name;
    /** @var  Source $Source */
    protected $Source;
    /** @var  \Sm\Resolvable\Resolvable $ReferenceResolvable This is the Resolvable that returns a Property that this Property is a Reference to */
    protected $ReferenceResolvable;
    /**
     * @var \Sm\Entity\Property\PropertyHaver[] $PropertyHavers The objects that hold this property
     */
    protected $PropertyHavers = [];
    /** @var  float $max_length The size limit of this Property */
    protected $max_length;
    
    
    /**
     * Property constructor.
     *
     * @param mixed|null  $name
     * @param Source|null $Source
     */
    public function __construct($name = null, Source $Source = null) {
        if (isset($name)) {
            $this->_name = $name;
        }
        if (isset($Source)) {
            $this->Source = $Source;
        }
    
        $this->ReferenceResolvable = NullResolvable::init();
        
        parent::__construct(null);
    }
    public function __get($name) {
        if ($name === 'object_id') {
            return $this->_object_id;
        }
        if ($name === 'reference') return $this->resolveReference();
        if ($name === 'potential_types') return $this->getPotentialTypes();
        # todo (bug or feature?) returns $this when "value" would resolves to null bc of variable resolution thing
        return parent::__get($name);
    }
    /**
     * Setter for this Property
     *
     * @param $name
     * @param $value
     *
     * @throws \Sm\Entity\Property\ReadonlyPropertyException
     */
    public function __set($name, $value) {
        if ($this->isReadonly()) throw new ReadonlyPropertyException("Cannot modify a readonly property");
        parent::__set($name, $value);
    }
    
    # region PropertyHavers
    /**
     * Sometimes multiple objects hold references to the same Property.
     * This allows the property to know which objects hold it.
     *
     * @param \Sm\Entity\Property\PropertyHaver $PropertyHaver
     *
     * @return $this
     */
    public function addPropertyHaver(PropertyHaver $PropertyHaver = null) {
        # If this PropertyContainer was previously owned by an EntityTypeVariable
        if (($this->PropertyHavers[0] ?? false) instanceof EntityTypeVariable) {
            return $this->setPropertyHaver($PropertyHaver);
        }
        if (!in_array($PropertyHaver, $this->PropertyHavers)) {
            $this->PropertyHavers[] = $PropertyHaver;
        }
        return $this;
    }
    /**
     * Remove an PropertyHaver from this Property
     *
     * @param \Sm\Entity\Property\PropertyHaver $PropertyHaver
     *
     * @return $this
     */
    public function removePropertyHaver(PropertyHaver $PropertyHaver) {
        $index = array_search($PropertyHaver, $this->PropertyHavers);
        if ($index !== false) {
            unset($this->PropertyHavers[ $index ]);
        }
        return $this;
    }
    /**
     * Make it so the only PropertyHaver of this Property
     *
     * @param \Sm\Entity\Property\PropertyHaver|null $PropertyHaver
     *
     * @return $this
     */
    public function setPropertyHaver(PropertyHaver $PropertyHaver = null) {
        $this->PropertyHavers = isset($PropertyHaver) ? [ $PropertyHaver ] : [];
        return $this;
    }
    /**
     * Get the array of objects that hold this Property
     *
     * @return \Sm\Entity\Property\PropertyHaver[]
     */
    public function getPropertyHavers() {
        return $this->PropertyHavers;
    }
    # endregion
    /**
     * Get the Source of the class
     *
     * @return Source
     */
    public function getSource(): Source {
        return $this->Source = $this->Source ?? NullSource::init();
    }
    /**
     * @param Source $Source
     *
     * @return Property
     */
    public function setSource(Source $Source) {
        $this->Source = $Source;
        return $this;
    }
    /**
     * If this property is a "mirror" of another property, this resolves the reference to that property
     *
     * @param \Sm\Resolvable\Resolvable|mixed $ReferenceResolvable
     *
     * @return Property
     */
    public function setReferenceResolvable($ReferenceResolvable): Property {
        if ($ReferenceResolvable instanceof Property) {
            $this->ReferenceResolvable = ResolvableResolvable::init($ReferenceResolvable);
            return $this;
        }
        
        if (!($ReferenceResolvable instanceof Resolvable)) {
            $this->ReferenceResolvable = $this->getFactoryContainer()->resolve(ResolvableFactory::class)->build($ReferenceResolvable);
        }
        
        return $this;
    }
    /**
     * Get the Property that this property is a reference to
     *
     * @return false|\Sm\Entity\Property\Property
     * @throws \Sm\Error\Error
     */
    public function resolveReference() {
        if (!isset($this->ReferenceResolvable)) {
            return false;
        }
        
        $result = $this->ReferenceResolvable->resolve();
        
        if (isset($result) && !($result instanceof Property)) {
            throw new Error("Can only resolve references to Properties! Please check the definition for {$this->name}");
        }
        
        return $result ?? false;
    }
    /**
     * @return string
     */
    public function getName(): string {
        return $this->_name;
    }
    /**
     * Set the name of the property
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name) {
        $this->_name = $name;
        return $this;
    }
    /**
     * @return float
     */
    public function getMaxLength() {
        return $this->max_length;
    }
    public function setMaxLength($max_length, $units = null) {
        if (isset($units)) throw new UnimplementedError("Not sure how to deal with units yet 😬");
        $this->max_length = (float)$max_length;
        return $this;
    }
}