<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 1:00 AM
 */

namespace Sm\View;


use Sm\App\App;
use Sm\Request\Request;
use Sm\Resolvable\ResolvableFactory;
use Sm\Response\Response;
use Sm\View\Template\Error\MalformedTemplateError;
use Sm\View\Template\Template;
use Sm\View\Template\TemplateFactory;


/**
 * Class View
 * A class meant to render templates built based on the existence of some resources
 *
 * @package Sm\View
 */
class View extends Response {
    protected $variables = [];
    /** @var Template[] */
    protected $templates = [];
    /** @var  App $App */
    protected $App;
    
    public function __construct($subject = null) {
        parent::__construct($subject);
    }
    
    #  Public methods
    #-----------------------------------------------------------------------------------
    /**
     * Render the View, return a string representative of its contents (in response to a request)
     *
     * The reason Requests are used here is because they provide with a consistent method of identifying
     * what we are trying to do with this View
     *
     * @param \Sm\Request\Request|null $Request
     *
     * @return mixed|string
     * @throws \Sm\Resolvable\Error\UnresolvableError
     */
    public function resolve(Request $Request = null) {
        $content_type =
            isset($Request)
                ? $Request->getRequestedContentType()
                : Response::TYPE_TEXT_HTML;
        
        # Get the template from the content type
        $Template = $this->templates[ $content_type ] ?? null;
        
        # If we don't have a template, convert whatever it is into a string
        if (!$Template)
            return "" . $this->_getResolvableFactory()->build($this->subject);
        
        # Resolve the template with the variables this View should get
        return $Template->resolve($this->getVariables());
    }
    /**
     * Register a template (or the path to a template) that
     *
     * @param string|Template $_template    Path to the template (assumed relative to the App) or instance of Template to use
     * @param string          $content_type The content type of the Template that we are setting.
     *
     * @return $this
     * @throws MalformedTemplateError
     */
    public function setTemplate($_template, $content_type = Response::TYPE_TEXT_HTML) {
        # If we don't know what to do with the template, throw an error
        if (!is_string($_template) && isset($_template) && !($_template instanceof Template)) {
            throw new MalformedTemplateError("Unable to create View from Template");
        }
        
        # Convert the template into a Template
        $Template =
            $this->_getTemplateFactory()
                 ->build($_template);
        if ($App = $this->getApp())
            $Template->setApp($App);
        
        $this->templates[ $content_type ] = $Template;
        return $this;
    }
    /**
     * @return App|null
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param App $App
     *
     * @return View
     */
    public function setApp(App $App): View {
        $this->App = $App;
        return $this;
    }
    
    #  Private/Protected methods
    #-----------------------------------------------------------------------------------
    /**
     * Get the variables that are going to be passed to the template.
     * This function should return an array, indexed by variable name, that has
     * stringifiable representations (usually Views) of the variable value;
     *
     * @return array
     */
    protected function getVariables() {
        $vars = [];
        
        # Get the variables from the subject
        if (is_array($this->subject)) {
            $vars = $this->subject;
        } else if (is_object($this->subject)) {
            $vars = get_class_vars($this->subject);
        }
        
        # Get the ViewFactory used to build the Views. This is dependent on the App;
        $ViewFactory = $this->_getViewFactory();
        
        # Create Views from the Values
        foreach ($vars as $k => $val)
            $vars[ $k ] = $ViewFactory->build($val)->resolve();
        
        
        return $vars;
    }
    /**
     * Get the ViewFactory that this instance of the View will use
     *
     * @return \Sm\View\ViewFactory
     */
    private function _getViewFactory() {
        /** @var ViewFactory $ViewFactory */
        if (isset($this->App)) $ViewFactory = $this->App->resolve('view.factory');
        if (!isset($ViewFactory)) $ViewFactory = new ViewFactory;
        return $ViewFactory;
    }
    /**
     * Get the TemplateFactory that this instance of the Template will use
     *
     * @return \Sm\View\Template\TemplateFactory
     */
    private function _getTemplateFactory() {
        /** @var TemplateFactory $TemplateFactory */
        if (isset($this->App)) $TemplateFactory = $this->App->resolve('template.factory');
        if (!isset($TemplateFactory)) $TemplateFactory = new TemplateFactory;
        return $TemplateFactory;
    }
    /**
     * Get the ResolvableFactory that this instance of the Resolvable will use
     *
     * @return ResolvableFactory
     */
    private function _getResolvableFactory() {
        /** @var ResolvableFactory $ResolvableFactory */
        if (isset($this->App)) $ResolvableFactory = $this->App->resolve('resolvable.factory');
        if (!isset($ResolvableFactory)) $ResolvableFactory = new ResolvableFactory;
        return $ResolvableFactory;
    }
}