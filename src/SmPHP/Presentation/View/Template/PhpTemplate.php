<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 12:56 PM
 */

namespace Sm\Presentation\View\Template;


class PhpTemplate extends Template {
    /**
     * @param array $variables
     *
     * @return string
     */
    protected function _include($variables = []): string {
        $_path_ = $this->resolved_path;
        
        # region  Capture the content of the file and return it region
        ob_start();
        
        # If there were variables passed in, extract them so they can be used in the script
        extract($variables, EXTR_SKIP);
        include $_path_;
        
        # Get the output of the include in a variable
        $output = ob_get_clean();
        # endregion
        
        return $output;
    }
}