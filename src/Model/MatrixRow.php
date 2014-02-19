<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MatrixRow extends Model
{
    protected $table = 'matrix_data';
    protected $primaryKey = 'row_id';

    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $this->whereIn('matrix_data.entry_id', $entryId);
    }
}
