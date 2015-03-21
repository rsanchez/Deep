<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\AbstractField;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\AbstractField
 */
abstract class AbstractFieldCollection extends PropertyCollection
{
    /**
     * Get the field_id for the specified field name
     *
     * @param  string $field name of the field
     * @return string
     */
    public function getFieldId($field)
    {
        return $this->getPropertyId($field);
    }

    /**
     * Check if this collection has the specified field name
     *
     * @param  string  $field the name of the field
     * @return boolean
     */
    public function hasField($field)
    {
        return $this->hasProperty($field);
    }
}
