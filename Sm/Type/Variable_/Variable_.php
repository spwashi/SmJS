<?php
/**
 * User: Sam Washington
 * Date: 2/10/17
 * Time: 10:23 PM
 */

namespace Sm\Type\Variable_;


use Sm\Resolvable\Error\UnresolvableError;
use Sm\Resolvable\Resolvable;

class Variable_ extends Resolvable {
    /** @var  Resolvable $subject */
    protected $subject;
    protected $name;
    protected $potential_types = [];
    /**
     * Make some things a little bit more convenient
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        if ($name === 'name' && $value) $this->name = $value;
    }
    /**
     * Get the name of the Variable
     *
     * @return string
     */
    public function __toString() {
        return $this->name ?? '';
    }
    /**
     * Get an array of the potential types that this Variable can be
     *
     * @return array
     */
    public function getPotentialTypes(): array {
        return $this->potential_types;
    }
    /**
     * Set an array of the potential types that a the value can be
     *
     * @param $potential_types
     *
     * @return mixed
     */
    public function setPotentialTypes(array $potential_types) {
        $this->potential_types = $potential_types;
        return $this;
    }
    /**
     * Set the value of this Variable. Subject must be
     *
     * @param Resolvable $subject
     *
     * @return $this
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function setSubject($subject) {
        if ($subject === null) return $this;
        # Only deal with Resolvables
        if (!($subject instanceof Resolvable)) {
            throw new UnresolvableError("Cannot set Subject to be something that is not resolvable");
        }
        
        $class = get_class($subject);
        
        # If we haven't given permission to set a Resolvable of this type, don't
        if ($len = count($this->potential_types)) {
            # iterate through the potential types and see if we're allowed to continue;
            for ($i = 0; $i < $len; $i++) {
                if ($class === $this->potential_types[ $i ] || is_subclass_of($class, $this->potential_types[ $i ])) {
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
     * @param $subject
     *
     * @return $this
     */
    public function setValue($subject) {
        return $this->setSubject($subject);
    }
    /**
     * Return the Value of the Variable or just return the Variable
     *
     * @return $this|mixed
     */
    public function resolve() {
        if (isset($this->subject)) return $this->subject->resolve();
        return $this;
    }
    /**
     * Does this Variable have a value to resolve to?
     *
     * @return bool
     */
    public function canResolve() {
        return isset($this->subject);
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
}