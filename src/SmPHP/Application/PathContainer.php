<?php
/**
 * User: Sam Washington
 * Date: 2/4/17
 * Time: 10:27 PM
 */

namespace Sm\Application;

use Sm\Core\Container\Container;


/**
 * Class PathContainer
 *
 * @package Sm\Application
 * @property string $base_path
 * @property string $config_path
 * @property string $app_path
 * @property string $template_path
 *
 * @method string|boolean base_path (string $path, boolean $do_verify = false)
 * @method string|boolean to_base (string $path, boolean $do_verify = false)
 * od@method string|boolean to_config (string $path, boolean $do_verify = false)
 * @method string|boolean to_template (string $path, boolean $do_verify = false)
 */
class PathContainer extends Container {
    public $App = null;
    /**
     * @return null
     */
    public function getApp() {
        return $this->App;
    }
    /**
     * @param null $App
     *
     * @return PathContainer
     */
    public function setApp($App) {
        $this->App = $App;
        return $this;
    }
    /**
     * @param null $name
     *
     * @return null|string
     */
    public function resolve($name = null) {
        $string = parent::resolve($name, $this, $this->App);
        if (!is_string($string)) {
            return $string;
        }
        return rtrim($string, '/') . '/';
    }
    function __call($name, $arguments) {
        $name = str_replace('to_', '', $name);
    
        # If we can resolve the name or something based on the name, assume that we are
        # just trying to append whatever to the path. We can also check if it's a file.
        if ($this->canResolve($name)) {
    
            $path = $this->resolve($name) . $arguments[0];
    
            return isset($arguments[1]) && (!file_exists($path)) ? false : $path;
        } else if ($this->canResolve("{$name}_path")) {
    
            return $this->__call("{$name}_path", $arguments);
        }
        return null;
    }
}