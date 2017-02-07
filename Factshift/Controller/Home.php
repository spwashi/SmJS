<?php
/**
 * User: Sam Washington
 * Date: 2/6/17
 * Time: 7:45 PM
 */

namespace Factshift\Controller;


use Sm\Controller\Controller;
use Sm\Request\Request;

class Home extends Controller {
    public function index(Request $request) {
        return $this->_createView()->setTemplate('test.php');
    }
}