<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GridRow extends Model
{
    protected $primaryKey = 'row_id';

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
