<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 1:00 AM
 */

namespace Sm\Presentation\View;


use Sm\Communication\Request\Request;
use Sm\Communication\Response\Response;
use Sm\Core\Resolvable\ResolvableFactory;
use Sm\Presentation\View\Template\Error\MalformedTemplateError;
use Sm\Presentation\View\Template\Template;
use Sm\Presentation\View\Template\TemplateFactory;


/**
 * Class View
 * A class meant to render templates built based on the existence of some resources
 *
 * @package Sm\Presentation\View
 */
class View extends Response {
    protected $variables = [];
    /** @var Template[] */
    protected $templates = [];
    
    
    #  Public methods
    #-----------------------------------------------------------------------------------
    /**
     * Render the View, return a string representative of its contents (in response to a request)
     *
     * The reason Requests are used here is because they provide with a consistent method of identifying
     * what we are trying to do with this View
     *
     * @param \Sm\Communication\Request\Request|null $Request
     *
     * @return mixed|string
     * @throws \Sm\Core\Resolvable\Error\UnresolvableError
     */
    public function resolve($Request = null) {
        if (!($Request instanceof Request)) $Request = null;
        $content_type =
            isset($Request)
                ? $Request->getRequestedContentType()
                : Response::TYPE_TEXT_HTML;
        
        # Get the template from the content type
        $Template = $this->templates[ $content_type ] ?? null;
        
        # If we don't have a template, convert whatever it is into a string
        if (!$Template) {
            return "" . $this->getFactoryContainer()->resolve(ResolvableFactory::class)
                             ->build($this->subject);
        }
        
        # Resolve the template with the variables this View should get
        return $Template->resolve($this->getVariables());
    }
    /**
     * Register a template (or the path to a template) that
     *
     * @param string|Template $_template    Path to the template (assumed relative to the Application) or instance of Template to use
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
        /** @var Template $Template */
        $Template =
            $this->getFactoryContainer()->resolve(TemplateFactory::class)
                 ->build($_template);
        
        $this->templates[ $content_type ] = $Template;
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
    public function getVariables(): array {
        $vars = [];
        
        # Get the variables from the subject
        if (is_array($this->subject)) {
            $vars = $this->subject;
        } else if (is_object($this->subject)) {
            $vars = get_class_vars($this->subject);
        }
        # Get the ViewFactory used to build the Views. This is dependent on the Application;
        /** @var \Sm\Presentation\View\ViewFactory $ViewFactory */
        $ViewFactory = $this->getFactoryContainer()->resolve(ViewFactory::class);
        
        # Create Views from the Values
        foreach ($vars as $k => $val) {
            $built_view = $ViewFactory->build($val)->resolve();
            if (strlen("{$built_view}")) {
                $vars[ $k ] = $built_view;
            }
        }
        
        
        return $vars;
    }
}