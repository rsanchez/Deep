<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\PropertyCollection;

/**
 * Interface for field/col models
 */
interface PropertyInterface
{
    /**
     * Get the property short name (eg. the field_name or col_name)
     *
     * @return string
     */
    public function getName();

    /**
     * Get the property ID with column suffix (eg. field_id_13 or col_id_13)
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get the property ID (eg. the field_id or col_id)
     *
     * @return string
     */
    public function getId();

    /**
     * Get the property type (eg. the field_type or col_type)
     *
     * @return string
     */
    public function getType();

    /**
     * Get the property prefix (eg. field or col)
     * @return string
     */
    public function getPrefix();

    /**
     * Get the property label
     * @return string
     */
    public function getLabel();

    /**
     * Get the property max length
     * @return int
     */
    public function getMaxLength();

    /**
     * Whether this property has child properties
     * (e.g. Matrix property has Cols)
     * @return bool
     */
    public function hasChildProperties();

    /**
     * Get child properties
     * (e.g. Matrix Cols)
     * @return \rsanchez\Deep\Collection\PropertyCollection
     */
    public function getChildProperties();

    /**
     * List of items for native select/checkboxes/radio buttons fields.
     * @return array
     */
    public function getListItems();

    /**
     * Whether the field is required
     * @return boolean
     */
    public function isRequired();

    /**
     * Set whether the field is required
     * @param boolean
     * @return void
     */
    public function setRequired($required = true);

    /**
     * Get the property's settings
     * @return array
     */
    public function getSettings();
}
