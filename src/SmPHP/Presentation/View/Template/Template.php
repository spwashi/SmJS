<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 11:32 PM
 */

namespace Sm\Presentation\View\Template;


use Sm\Application\App;
use Sm\Core\Formatting\Formatter\Formatter;
use Sm\Core\Resolvable\AbstractResolvable;
use Sm\Core\Resolvable\StringResolvable;

/**
 * Class Template
 *
 * Defines
 *
 * @package Sm\Presentation\View\Template
 */
abstract class Template extends AbstractResolvable implements Formatter {
    protected $content_type;
    protected $resolved_path    = null;
    protected $path_is_absolute = false;
    protected $expected_variables;
    protected $error;
    /** @var  App $App */
    protected $App;
    /**
     * @param string                   $item
     * @param \Sm\Application\App|null $App
     *
     * @return static
     */
    public static function init($item = null) {
        return new static($item);
    }
    /**
     * Fill the template from an array of passed-in variables, return a string
     *
     * @param array $_
     *
     * @return string
     */
    public function resolve($_ = []) {
        $this->_resolvePath();
        if (isset($this->error)) {
            $e           = $this->error;
            $this->error = null;
            throw $e;
        }
        return $this->_include(is_array($_) ? $_ : null);
    }
    /**
     * Set the "Subject" of the template. In this case, this is the path.
     *
     * @param      $_path_
     * @param bool $_is_absolute_
     *
     * @return $this
     */
    public function setSubject($_path_, $_is_absolute_ = false) {
        $this->path_is_absolute = $_is_absolute_;
        $this->subject          = $_path_;
        $this->resolved_path    = null;
        return $this;
    }
    /**
     * Fill the template with the passed-in array of variables, return the output as a string
     *
     * @param array $variables
     *
     * @return string
     */
    abstract protected function _include($variables = []): string;
    /**
     * Resolve the path now that everything else about the template is all good
     *
     * @return string
     */
    private function _resolvePath() {
        # If we've already resolved the path, don't do it again.
        if (isset($this->resolved_path)) return $this->resolved_path;
        
        /** @var string $_path_ */
        $_path_ = $this->subject;
        
        # We must have an app in order for the path to be relative to something!
        if (!isset($this->App)) $this->path_is_absolute = true;
        
        
        # If the path isn't a string, we don't know what to do with it
        if (!is_string($_path_)) {
            $_path_      = StringResolvable::init($_path_);
            $this->error = new Error\MalformedTemplateError("The requested route '{$_path_}' has not been created correctly");
            return $this->resolved_path = null;
        }
        # If the path isn't existent, save an error to throw later
        if (!is_file($_path_)) {
            $_path_      = StringResolvable::init($_path_);
            $this->error = new Error\MalformedTemplateError("The requested template '{$_path_}' does not exist" . ($this->App ? "in {$this->App->name}" : '.'));
            return $this->resolved_path = null;
        }
        $this->error = null;
        return $this->resolved_path = $_path_;
    }
}