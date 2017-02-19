<?php
/**
 * User: Sam Washington
 * Date: 2/19/17
 * Time: 12:50 AM
 */

namespace Sm\Factory;


use Sm\IoC\IoC;

class FactoryContainer extends IoC {
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
        if (!is_string($string)) return $string;
        return rtrim($string, '/') . '/';
    }
}