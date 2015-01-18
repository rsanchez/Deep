<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Collection\FieldCollection;

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
    public function newCollection(array $fields = array())
    {
        return new FieldCollection($fields);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildProperties()
    {
        return $this->field_type === 'matrix' || $this->field_type === 'grid';
    }

    /**
     * {@inheritdoc}
     */
    public function getChildProperties()
    {
        switch ($this->field_type) {
            case 'matrix':
                return $this->matrix_cols;
            case 'grid':
                return $this->grid_cols;
        }

        return parent::getChildProperties();
    }

    /**
     * Define the matrix_cols Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasOneFromRepository
     */
    public function matrixCols()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\MatrixCol', 'field_id', 'field_id');
    }

    /**
     * Define the grid_columns Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasOneFromRepository
     */
    public function gridCols()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\GridCol', 'field_id', 'field_id');
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
