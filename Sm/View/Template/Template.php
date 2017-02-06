<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 11:32 PM
 */

namespace Sm\View\Template;


use Sm\App\App;
use Sm\Resolvable\Resolvable;
use Sm\Resolvable\StringResolvable;

abstract class Template extends Resolvable {
    protected $content_type;
    protected $expected_variables;
    protected $error;
    /** @var  App $App */
    protected $App;
    
    /**
     * Template constructor.
     *
     * @param null             $subject
     * @param \Sm\App\App|null $App
     */
    public function __construct($subject, App $App = null) {
        if (isset($App)) $this->setApp($App);
        parent::__construct($subject);
    }
    /**
     * Set the Application that the Template would be acting relative to
     *
     * @param \Sm\App\App $app
     *
     * @return $this
     */
    public function setApp(App $app = null) {
        $this->App = $app;
        return $this;
    }
    
    /**
     * @param string           $item
     * @param \Sm\App\App|null $App
     *
     * @return static
     */
    public static function init($item = null, App $App = null) {
        return new static($item, $App);
    }
    /**
     * Fill the template from an array of passed-in variables, return a string
     *
     * @param array $variables
     *
     * @return string
     */
    public function resolve($variables = [ ]) {
        if (isset($this->error)) {
            $e           = $this->error;
            $this->error = null;
            throw $e;
        }
        return $this->_include(is_array($variables) ? $variables : null);
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
        # We must have an app in order for the path to be relative to something!
        if (!isset($this->App)) $_is_absolute_ = true;
        
        # If the path isn't a string, we don't know what to do with it
        if (!is_string($_path_)) {
            $_path_      = StringResolvable::coerce($_path_);
            $this->error = new MalformedTemplateException("The requested route '{$_path_}' has not been created correctly");
            return $this;
        }
        
        # If the path is not absolute, make it relative to the "templates" path
        if (!$_is_absolute_) $_path_ = $this->App->Paths->to_template($_path_);
        
        # If the path isn't existent, save an error to throw later
        if (!is_file($_path_)) {
            $_path_      = StringResolvable::coerce($_path_);
            $this->error = new MalformedTemplateException("The requested template '{$_path_}' does not exist");
            return $this;
        }
        $this->error   = null;
        $this->subject = $_path_;
        
        return $this;
    }
    /**
     * Fill the template with the passed-in array of variables, return the output as a string
     *
     * @param array $variables
     *
     * @return string
     */
    abstract protected function _include($variables = [ ]) :string;
}