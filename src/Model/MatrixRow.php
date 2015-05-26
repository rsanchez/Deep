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
use rsanchez\Deep\Relations\HasOneFromRepository;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model for the matrix_data table
 */
class MatrixRow extends AbstractEntity
{
    use HasFieldRepositoryTrait;

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
    protected $hidden = ['site_id', 'entry_id', 'field_id', 'var_id', 'is_draft', 'row_order'];

    /**
     * {@inheritdoc}
     */
    protected $hiddenAttributesRegex = '/^col_id_\d+$/';

    /**
     * {@inheritdoc}
     *
     * @param  array                                         $models
     * @return \rsanchez\Deep\Collection\MatrixRowCollection
     */
    public function newCollection(array $models = [])
    {
        return new MatrixRowCollection($models);
    }

    /**
     * Define the MatrixCol Eloquent relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cols()
    {
        return new HasMany(
            (new MatrixCol())->newQuery(),
            $this,
            'matrix_cols.field_id',
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
        $entryId = is_array($entryId) ? $entryId : [$entryId];

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
        $this->setRelation('field', $field);

        $this->attributes['field_id'] = $field->field_id;

        $this->setCols($field->getChildProperties());
    }

    /**
     * Set the Matrix columns for this row
     *
     * @param  \rsanchez\Deep\Collection\MatrixColCollection $cols
     * @return void
     */
    public function setCols(MatrixColCollection $cols)
    {
        $this->setRelation('cols', $cols);

        $this->setDehydrators($this->getHydratorFactory()->getDehydrators($cols));

        $this->hydrateDefaultProperties();
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
