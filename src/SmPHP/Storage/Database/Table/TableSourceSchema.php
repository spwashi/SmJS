<?php
/**
 * User: Sam Washington
 * Date: 7/20/17
 * Time: 10:12 PM
 */

namespace Sm\Storage\Database\Table;


use Sm\Core\Internal\Identification\Identifiable;
use Sm\Data\Source\DataSourceSchema;

interface TableSourceSchema extends DataSourceSchema, Identifiable {
    public function getName();
}