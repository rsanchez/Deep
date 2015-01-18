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
use rsanchez\Deep\Validation\ValidatableInterface;

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
     * {@inheritdoc}
     */
    protected $hiddenAttributesRegex = '/^col_id_\d+$/';

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
     * @return \rsanchez\Deep\Collection\GridColCollection|null
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
        return $this->getCols() ?: new GridColCollection();
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
        $this->cols = $cols;

        foreach ($cols as $col) {
            if (! isset($this->customFields[$col->getName()])) {
                $this->setCustomField($col->getName(), $this->{$col->getIdentifier()});
            }
        }
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
