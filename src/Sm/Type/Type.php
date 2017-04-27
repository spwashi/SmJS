<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 8:46 PM
 */

namespace Sm\Type;


use Sm\Resolvable\Resolvable;
use Sm\Resolvable\ResolvableFactory;

class Type extends Resolvable implements \JsonSerializable {
    /** @var  Resolvable $subject */
    public $subject;
    public function __construct($subject) {
        $subject = static::resolveType($subject);
        parent::__construct($subject);
    }
    /**
     * @param null|mixed $_ ,..
     *
     * @return mixed
     */
    public function resolve($_ = null) {
        return $this->subject->resolve();
    }
    function jsonSerialize() {
        $subject = "$this->subject";
        if (strlen($subject)) {
            return $subject;
        }
        $class = static::class;
        $expl  = explode('\\', $class);
        end($expl);
        return "[" . $expl[ key($expl) ] . "]";
    }
    public static function resolveType($subject) {
        return ResolvableFactory::coerce($subject);
    }
}