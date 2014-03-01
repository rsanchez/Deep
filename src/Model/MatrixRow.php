<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\MatrixRowCollection;

/**
 * Model for the matrix_data table
 */
class MatrixRow extends Model
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'matrix_data';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'row_id';

    /**
     * {@inheritdoc}
     *
     * @param  array                                         $models
     * @return \rsanchez\Deep\Collection\MatrixRowCollection
     */
    public function newCollection(array $models = array())
    {
        return new MatrixRowCollection($models);
    }

    /**
     * Filter by Entry ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int|array                             $entryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntryId(Builder $query, $entryId)
    {
        $entryId = is_array($entryId) ? $entryId : array($entryId);

        return $query->whereIn('matrix_data.entry_id', $entryId);
    }

    /**
     * {@inheritdoc}
     */
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
