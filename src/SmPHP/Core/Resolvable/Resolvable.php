<?php
/**
 * User: Sam Washington
 * Date: 1/25/17
 * Time: 7:56 PM
 */

namespace Sm\Core\Resolvable;


use Sm\Core\Factory\HasFactoryContainerTrait;
use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Core\Internal\Identification\Identifier;
use Sm\Core\Util;

/**
 * Class Resolvable
 *
 * Represents something that will eventually have an end value (vague, I know)
 * Basically, this class is meant to provide a consistent interface for interacting
 * with the various objects/types that we might come across in this framework.
 *
 * @package Sm\Core\Resolvable
 */
abstract class Resolvable implements Identifiable, \Sm\Core\Abstraction\Resolvable\Resolvable {
    use HasFactoryContainerTrait;
    use HasObjectIdentityTrait;
    /** @var  mixed $subject The thing that this Resolvable is wrapping */
    protected $subject;
    
    /**
     * Resolvable constructor.
     *
     * @param mixed $subject
     */
    public function __construct($subject = null) {
        $this->setSubject($subject);
    
        # Makes it easy to refer to Resolvables or whatever
        $this->setObjectId($this->createIdentity());
    }
    /**
     * Get the subject of the Resolvable (The thing that this Resolvable is wrapping)
     *
     * @see Resolvable::$subject
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
    }
    /**
     * Set the subject that the Resolvable is going to use as a reference
     *
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }
    /**
     * Convert this to a string
     *
     * @return string
     */
    public function __toString() {
        if ($this->subject !== $this && Util::canBeString($this->subject)) {
            return "$this->subject";
        } else {
            $resolved = $this->resolve();
            if ($resolved !== $this && Util::canBeString($resolved)) {
                return "$resolved";
            } else {
                return '';
            }
        }
    }
    /**
     * Resolve this Resolvable with arguments
     *
     * @return mixed
     */
    public function __invoke() {
        return $this->resolve(...func_get_args());
    }
    /**
     * Revert a Resolvable back to its original state.
     * Only really meant to be used in cases where Resolvables are inherited
     *
     * @return $this
     */
    public function reset() {
        return $this;
    }
    /**
     * Static constructor for resolvables
     *
     * @param mixed $item
     *
     * @return static
     */
    public static function init($item = null) {
        return new static($item);
    }
    /**
     * Convert something to a Resolvable. Same thing as init usually (oops)
     *
     * @param mixed $item
     *
     * @return static|$this|Resolvable
     */
    public static function coerce($item = null) {
        if (is_a($item, static::class)) {
            return $item;
        }
        return static::init(...func_get_args());
    }
    protected function createIdentity() {
        return Identifier::generateIdentity($this);
    }
}