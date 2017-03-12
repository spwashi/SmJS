<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 11:51 PM
 */

namespace Sm\View\Template;


use Sm\Formatter\FormatterFactory;

class TemplateFactory extends FormatterFactory {
    /**
     * Return whatever this factory refers to based on some sort of operand
     *
     * @param $operand
     *
     * @return Template
     */
    public function build($operand = null) {
        if ($operand instanceof Template) return $operand;
        return new PhpTemplate(is_string($operand) ? $operand : null);
    }
}