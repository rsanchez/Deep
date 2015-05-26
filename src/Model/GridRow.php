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
use rsanchez\Deep\Relations\HasOneFromRepository;
use rsanchez\Deep\Relations\GridRowHasMany;

/**
 * Model for the channel_grid_field_X table(s)
 */
class GridRow extends AbstractEntity
{
    use HasFieldRepositoryTrait;

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $primaryKey = 'row_id';

    /**
     * {@inheritdoc}
     */
    protected $hidden = ['site_id', 'entry_id', 'field_id', 'row_order'];

    /**
     * {@inheritdoc}
     */
    protected $hiddenAttributesRegex = '/^col_id_\d+$/';

    /**
     * {@inheritdoc}
     *
     * @param  array                                       $models
     * @return \rsanchez\Deep\Collection\GridRowCollection
     */
    public function newCollection(array $models = [])
    {
        return new GridRowCollection($models);
    }

    /**
     * Define the GridCol Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cols()
    {
        return new GridRowHasMany(
            (new GridCol())->newQuery(),
            $this,
            'grid_columns.field_id',
            'field_id'
        );
    }

    /**
     * Define the Field Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function field()
    {
        return new HasOneFromRepository(
            static::getFieldRepository()->getModel()->newQuery(),
            $this,
            'channel_fields.field_id',
            'field_id',
            static::getFieldRepository()
        );
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
     * Set the field_id attribute for this entry
     * @param $fieldId
     */
    public function setFieldIdAttribute($fieldId)
    {
        $this->setField(static::getFieldRepository()->find($fieldId));
    }

    /**
     * Set the field_id attribute for this entry
     * @param $fieldId
     */
    public function setField(Field $field)
    {
        $this->setTable('channel_grid_field_'.$field->getId());

        $this->setCols($field->getChildProperties());
    }

    /**
     * Get the field_id attribute for this entry,
     * derived from the table name
     * @return string
     */
    public function getFieldIdAttribute()
    {
        return substr($this->table, 19);//strlen('channel_grid_field_')
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
        $entryId = is_array($entryId) ? $entryId : [$entryId];

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
        $this->setRelation('cols',  $cols);

        $this->setDehydrators($this->getHydratorFactory()->getDehydrators($cols));

        $this->hydrateDefaultProperties();
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
