<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Model\Field\Matrix;
use rsanchez\Deep\Model\Field\Grid;

/**
 * Model for the channel_fields table
 */
class Field extends AbstractField
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'channel_fields';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'group_id';

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $fields
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function newCollection(array $fields = [])
    {
        return new FieldCollection($fields);
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array       $attributes
     * @param  string|null $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        if (isset($attributes['field_type'])) {
            switch ($attributes['field_type']) {
                case 'matrix':
                    $instance = new Matrix();
                    break;
                case 'grid':
                    $instance = new Grid();
                    break;
            }

            if (isset($instance)) {
                $instance->exists = true;

                $instance->setRawAttributes((array) $attributes, true);

                return $instance;
            }
        }

        return parent::newFromBuilder($attributes, $connection);
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        $settings = parent::getSettings();

        if ($fieldSettings = @unserialize(base64_decode($this->field_settings))) {
            $settings = array_merge($settings, $fieldSettings);
        }

        return $settings;
    }
}
