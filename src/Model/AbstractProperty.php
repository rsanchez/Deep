<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Model\Model;

/**
 * Interface for field/col models
 */
abstract class AbstractProperty extends Model
{
    /**
     * Get the property short name (eg. the field_name or col_name)
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get the property ID with column suffix (eg. field_id_13 or col_id_13)
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Get the property ID (eg. the field_id or col_id)
     *
     * @return string
     */
    abstract public function getId();

    /**
     * Get the property type (eg. the field_type or col_type)
     *
     * @return string
     */
    abstract public function getType();
}
