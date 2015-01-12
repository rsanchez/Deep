<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

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

    /**
     * Get the property prefix (eg. field or col)
     * @return string
     */
    abstract public function getPrefix();

    /**
     * Get the property label
     * @return string
     */
    abstract public function getLabel();

    /**
     * Get the property max length
     * @return int
     */
    abstract public function getMaxLength();

    /**
     * Whether the field is required
     * @return boolean
     */
    public function isRequired()
    {
        return false;
    }

    /**
     * Set whether the field is required
     * @param boolean
     * @return void
     */
    public function setRequired($required = true)
    {
    }

    /**
     * Get the property's settings
     * @return array
     */
    public function getSettings()
    {
        return [];
    }
}
