<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\GridRowCollection;

class GridRow extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'row_id';

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $models
     * @return \rsanchez\Deep\Collection\GridRowCollection
     */
    public function newCollection(array $models = array())
    {
        return new GridRowCollection($models);
    }

    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $query->whereIn('entry_id', $entryId);
    }

    public function scopeFieldId(Builder $query, $fieldId)
    {
        return $query->from('channel_grid_field_'.$fieldId);
    }

    public function toArray()
    {
        $array = parent::toArray();

        foreach ($array as &$row) {
            if (method_exists($row, 'toArray')) {
                $row = $row->toArray();
            }
        }

        return $array;
    }
}
