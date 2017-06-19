<?php
/**
 * User: spwashi2
 * Date: 2/2/2017
 * Time: 5:23 PM
 */

namespace Sm\Core\Resolvable;

use Sm\Core\Resolvable\Error\UnresolvableError;

/**
 * Class DateResolvable
 *
 * Class that represents a date
 *
 * @package Sm\Core\Resolvable
 */
class DateResolvable extends Resolvable {
    public function __construct($subject = null) {
        if (!isset($subject)) {
            $subject = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
        } else {
            throw new UnresolvableError("Cannot yet resolve dates from other types");
        }
        
        parent::__construct($subject);
    }
    
    public function resolve($arguments = null) {
        return $this->subject;
    }
}