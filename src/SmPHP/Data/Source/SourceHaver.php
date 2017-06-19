<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 1:42 PM
 */

namespace Sm\Data\Source;


/**
 * Interface SourceHaver
 *
 * An interface to define something as having a DataSource.
 *
 * @package Sm\Data\ORM\EntityType\DataSource
 */
interface SourceHaver {
    /**
     * Return the DataSource of ths object
     *
     * @return \Sm\Data\Source\DataSource
     */
    public function getSource(): DataSource;
}