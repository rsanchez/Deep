<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\GridRowCollection;
use rsanchez\Deep\Collection\GridColCollection;

/**
 * Model for the channel_grid_field_X table(s)
 */
class GridRow extends AbstractEntity
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'row_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = array('site_id', 'entry_id', 'field_id', 'row_order');

    /**
     * Cols associated with this row
     * @var \rsanchez\Deep\Collection\GridColCollection
     */
    protected $cols;

    /**
     * {@inheritdoc}
     *
     * @param  array                                       $models
     * @return \rsanchez\Deep\Collection\GridRowCollection
     */
    public function newCollection(array $models = array())
    {
        return new GridRowCollection($models);
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
        return 'grid';
    }

    /**
     * Get the Grid columns associated with this row
     *
     * @return \rsanchez\Deep\Collection\GridColCollection
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->getCols();
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

        return $query->whereIn('entry_id', $entryId);
    }

    /**
     * Filter by Field ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $fieldId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFieldId(Builder $query, $fieldId)
    {
        return $query->from('channel_grid_field_'.$fieldId);
    }

    /**
     * Set the Grid columns for this row
     *
     * @param  \rsanchez\Deep\Collection\GridColCollection $cols
     * @return void
     */
    public function setCols(GridColCollection $cols)
    {
        $row = $this;

        $this->cols = $cols;

        $cols->each(function ($col) use ($row) {
            $hidden = $row->getHidden();

            $hidden[] = 'col_id_'.$col->col_id;

            $row->setHidden($hidden);

            $row->setAttribute($col->col_name, $row->getAttribute('col_id_'.$col->col_id));
        });
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

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'row';
    }
}
