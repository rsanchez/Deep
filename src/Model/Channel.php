<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\ChannelCollection;

/**
 * Model for the channels table
 */
class Channel extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'channels';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'channel_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = array('fields');

    /**
     * {@inheritdoc}
     *
     * @param  array                                       $models
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function newCollection(array $models = array())
    {
        return new ChannelCollection($models);
    }

    /**
     * Get channel fields of the specified type
     * @param  string                                    $type name of a fieldtype
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function fieldsByType($type)
    {
        return $this->fields->filter(function ($field) use ($type) {
            return $field->field_type === $type;
        });
    }

    /**
     * Get the cat_group attribute as an array
     * @param  string $data pipe-delimited list
     * @return array of category group IDs
     */
    public function getCatGroupAttribute($data)
    {
        return $data ? explode('|', $data) : array();
    }

    /**
     * Return the channel_name when cast to string
     *
     * @var string
     */
    public function __toString()
    {
        return $this->channel_name;
    }
}
