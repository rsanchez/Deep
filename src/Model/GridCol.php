<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Model;

use Illuminate\Database\Eloquent\Builder;
use rsanchez\Deep\Collection\GridColCollection;

/**
 * Model for the grid_columns table
 */
class GridCol extends AbstractProperty
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $table = 'grid_columns';

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
    public function scopeFieldId(Builder $query, $fieldId)
    {
        $fieldId = is_array($fieldId) ? $fieldId : array($fieldId);

        return $this->whereIn('field_id', $fieldId);
    }

    /**
     * {@inheritdoc}
     *
     * @param  array                                       $models
     * @return \rsanchez\Deep\Collection\GridColCollection
     */
    public function newCollection(array $models = array())
    {
        return new GridColCollection($models);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->col_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'col_id_'.$this->col_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->col_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->col_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return 'col';
    }
}
