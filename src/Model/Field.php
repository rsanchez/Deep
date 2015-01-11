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

    public function hasRows()
    {
        return $this->field_type === 'matrix' || $this->field_type === 'grid';
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
