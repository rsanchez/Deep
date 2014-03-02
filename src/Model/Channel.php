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
use rsanchez\Deep\Model\Builder;

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
     * A link to the Builder's relation cache
     * @var array
     */
    protected $builderRelationCache;

    /**
     * Define the Channel Fields Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('\\rsanchez\\Deep\\Model\\Field', 'group_id', 'field_group');
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $models
     * @return \rsanchez\Deep\Collection\ChannelCollection
     */
    public function newCollection(array $models = array())
    {
        $collection = new ChannelCollection($models);

        $collection->fields = $this->builderRelationCache['fields'];

        return $collection;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new Builder($query);

        $this->builderRelationCache =& $builder->relationCache;

        return $builder;
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
}
