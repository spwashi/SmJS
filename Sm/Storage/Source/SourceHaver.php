<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 1:42 PM
 */

namespace Sm\Storage\Source;


/**
 * Interface SourceHaver
 *
 * An interface to define something as having a Source.
 *
 * @package Sm\Storage\Source
 */
interface SourceHaver {
    /**
     * Return the Source of ths object
     *
     * @return \Sm\Storage\Source\Source
     */
    public function getSource(): Source;
}