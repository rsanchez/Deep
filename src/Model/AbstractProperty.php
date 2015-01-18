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
abstract class AbstractProperty extends Model implements PropertyInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasChildProperties()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildProperties()
    {
        return new PropertyCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getListItems()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired($required = true)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxLength()
    {
        return 255;
    }
}
