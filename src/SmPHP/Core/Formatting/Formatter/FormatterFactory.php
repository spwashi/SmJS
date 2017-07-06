<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 1:53 PM
 */

namespace Sm\Core\Formatting\Formatter;


use Sm\Core\Factory\AbstractFactory;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;

class FormatterFactory extends AbstractFactory {
    public function build() {
        try {
            return parent::build(...func_get_args());
        } catch (FactoryCannotBuildException $e) {
            return (new PlainStringFormatter);
        }
    }
}