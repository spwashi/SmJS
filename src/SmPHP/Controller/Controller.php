<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 8:05 PM
 */

namespace Sm\Controller;


use Sm\App\App;
use Sm\View\View;

class Controller {
    /** @var \Sm\App\App $App */
    protected $App;
    public function __construct(App $App = null) {
        $this->App = $App;
    }
    /**
     * Create a typical View for this class
     *
     * @param mixed|null $subject
     *
     * @return \Sm\View\View
     */
    public function _createView($subject = null) {
        return View::init($subject)->setApp($this->App);
    }
}