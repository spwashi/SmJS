<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 1:42 PM
 */

namespace Sm\Entity\Source;


/**
 * Interface SourceHaver
 *
 * An interface to define something as having a Source.
 *
 * @package Sm\Entity\Source
 */
interface SourceHaver {
    /**
     * Return the Source of ths object
     *
     * @return \Sm\Entity\Source\DataSource
     */
    public function getSource(): DataSource;
}