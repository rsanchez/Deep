<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use rsanchez\Deep\Repository\FieldRepository;
use rsanchez\Deep\Collection\ChannelCollection;
use rsanchez\Deep\Relations\HasManyFromRepository;

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
     * Global Field Repository
     * @var \rsanchez\Deep\Repository\FieldRepository
     */
    public static $fieldRepository;

    /**
     * Set the global FieldRepository
     * @param  \rsanchez\Deep\Repository\FieldRepository $fieldRepository
     * @return void
     */
    public static function setFieldRepository(FieldRepository $fieldRepository)
    {
        self::$fieldRepository = $fieldRepository;
    }

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
     * Define the Fields Eloquent relationship
     * @return \rsanchez\Deep\Relations\HasManyFromRepository
     */
    public function fields()
    {
        return new HasManyFromRepository(
            self::$fieldRepository->getModel()->newQuery(),
            $this,
            'channel_fields.group_id',
            'field_group',
            self::$fieldRepository,
            'getFieldsByGroup'
        );
    }

    /**
     * Get channel fields of the specified type
     * @param  string                                    $type name of a fieldtype
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function fieldsByType($type)
    {
        return $this->fields->getFieldsByFieldtype($type);
    }

    /**
     * Get the cat_group attribute as an array
     * @param  string $data pipe-delimited list
     * @return array  of category group IDs
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
