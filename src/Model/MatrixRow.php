<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\MatrixRowCollection;
use rsanchez\Deep\Collection\MatrixColCollection;
use rsanchez\Deep\Validation\ValidatableInterface;

/**
 * Model for the matrix_data table
 */
class MatrixRow extends AbstractEntity
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
     */
    protected $hidden = array('site_id', 'entry_id', 'field_id', 'var_id', 'is_draft', 'row_order');

    /**
     * {@inheritdoc}
     */
    protected $hiddenPatterns = ['/^col_id_\d+$/'];

    /**
     * Cols associated with this row
     * @var \rsanchez\Deep\Collection\MatrixColCollection
     */
    protected $cols;

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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->row_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'matrix';
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
    public function getProperties()
    {
        return $this->getCols() ?: new MatrixColCollection();
    }

    /**
     * Set the Matrix columns for this row
     *
     * @param  \rsanchez\Deep\Collection\MatrixColCollection $cols
     * @return void
     */
    public function setCols(MatrixColCollection $cols)
    {
        $this->cols = $cols;
    }

    /**
     * Get the Matrix columns associated with this row
     *
     * @return \rsanchez\Deep\Collection\MatrixColCollection|null
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'row';
    }

    /**
     * {@inheritdoc}
     */
    public function shouldValidateIfChild()
    {
        return true;
    }
}
