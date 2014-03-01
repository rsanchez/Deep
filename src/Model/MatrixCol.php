<?php

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\MatrixColCollection;

class MatrixCol extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'matrix_cols';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'col_id';

    public function scopeFieldId(Builder $query, $fieldId)
    {
        $fieldId = is_array($fieldId) ? $fieldId : array($fieldId);

        return $this->whereIn('field_id', $fieldId);
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                     $models
     * @return \rsanchez\Deep\Collection\MatrixColCollection
     */
    public function newCollection(array $models = array())
    {
        return new MatrixColCollection($models);
    }
}
