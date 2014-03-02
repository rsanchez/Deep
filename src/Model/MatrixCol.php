<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use rsanchez\Deep\Collection\MatrixColCollection;

/**
 * Model for the matrix_cols table
 */
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

    /**
     * Filter by Field ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $fieldId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFieldId(EloquentBuilder $query, $fieldId)
    {
        $fieldId = is_array($fieldId) ? $fieldId : array($fieldId);

        return $this->whereIn('field_id', $fieldId);
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                         $models
     * @return \rsanchez\Deep\Collection\MatrixColCollection
     */
    public function newCollection(array $models = array())
    {
        return new MatrixColCollection($models);
    }
}
