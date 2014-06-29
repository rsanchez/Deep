<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for field/col models
 */
abstract class AbstractProperty extends Model
{
    /**
     * Get the property short name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get the property ID with column suffix (eg. field_id_13)
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Get the property ID
     *
     * @return string
     */
    abstract public function getId();

    /**
     * Get the property type
     *
     * @return string
     */
    abstract public function getType();
}
