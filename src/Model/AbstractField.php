<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Interface for field models
 */
abstract class AbstractField extends AbstractProperty
{
    /**
     * Get the field short name
     *
     * @return string
     */
    public function getFieldNameAttribute($value)
    {
        return $value;
    }

    /**
     * Get the field ID
     *
     * @return string
     */
    public function getFieldIdAttribute($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->field_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'field_id_'.$this->field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxLength()
    {
        return $this->field_maxl;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->field_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->field_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->field_label;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->field_required === 'y';
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired($required = true)
    {
        return $this->field_required = $required ? 'y' : 'n';
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getListItems()
    {
        return $this->field_list_items ? explode("\n", $this->field_list_items) : [];
    }
}
