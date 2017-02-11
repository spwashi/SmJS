<?php
/**
 * User: Sam Washington
 * Date: 2/6/17
 * Time: 7:45 PM
 */

namespace Factshift\Controller;


use Sm\Controller\Controller;
use Sm\Request\Request;
use Sm\View\View;

class Home extends Controller {
    
    /**
     * The initial entry point of an app
     *
     * @param \Sm\Request\Request $request
     *
     * @return View
     */
    public function index(Request $request) {
        return
            $this
                ->_createView()
                ->setTemplate('test.php');
    }
}