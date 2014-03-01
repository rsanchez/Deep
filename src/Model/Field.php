<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\FieldCollection;

/**
 * Model for the channel_fields table
 */
class Field extends Model
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
     * Define the Fieldtype Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fieldtype()
    {
        return $this->hasOne('\\rsanchez\\Deep\\Model\\Fieldtype', 'name', 'field_type');
    }

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
}
