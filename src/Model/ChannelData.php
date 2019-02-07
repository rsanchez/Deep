<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

/**
 * Model for the channel_data table
 */
class ChannelData extends Model
{
    use HasFieldRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    protected $table = 'channel_data';

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'entry_id';

    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'site_id' => 'required|exists:sites,site_id',
        'channel_id' => 'required|exists:channels,channel_id',
        'entry_id' => 'required|exists:channel_titles,entry_id',
    ];

    /**
     * {@inheritdoc}
     */
    public function setRawAttributes(array $attributes, $sync = false)
    {
        $attributesByField = [];

        foreach ($attributes as $name => $value) {
            if (preg_match('/^field_(id|ft|dt)_(\d+)$/', $name, $match)) {
                $fieldId = $match[1];

                $field = self::getFieldRepository()->find($fieldId);

                if ($field && $field->legacy_field_data !== 'y') {
                    $attributesByField[$fieldId][$name] = $value;

                    unset($attributes[$name]);
                }
            }
        }

        parent::setRawAttributes($attributes);

        foreach ($attributesByField as $fieldId => $attributes) {
            $table = "channel_data_field_{$fieldId}";

            if ($this->entry_id) {
                $attributes['entry_id'] = $this->entry_id;
            }

            $this->relations[$table] = new ChannelDataField($attributes);

            $this->relations[$table]->setTable($table);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $relation = null;

        if (preg_match('/^field_(id|ft|dt)_\d+$/', $key, $match)) {
            $fieldId = $match[1];

            $field = self::getFieldRepository()->find($fieldId);

            if ($field && $field->legacy_field_data !== 'y') {
                $table = "channel_data_field_{$fieldId}";

                $relation = $this->relations[$table];
            }
        }

        if ($relation) {
            $relation->setAttribute($key, $value);
        } else {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        foreach ($this->relations as $relation) {
            $attributes = array_merge($attributes, $relation->getAttributes());
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function newQuery($excludeDeleted = true)
    {
        $query = parent::newQuery($excludeDeleted);

        // join all the channel_data_field_X tables
        foreach (static::getFieldRepository()->getFields() as $field) {
            if ($field->legacy_field_data !== 'y') {
                $table = "channel_data_field_{$field->field_id}";

                $query->leftJoin($table, 'channel_data.entry_id', '=', "{$table}.entry_id");
            }
        }

        return $query;
    }
}
