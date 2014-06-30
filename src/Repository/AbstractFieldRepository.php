<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Model\AbstractField;

/**
 * Repository of all Fields
 */
abstract class AbstractFieldRepository extends AbstractDeferredRepository
{
    /**
     * Array of Field keyed by field_name
     * @var array
     */
    protected $fieldsByName = array();

    /**
     * Array of Field keyed by field_id
     * @var array
     */
    protected $fieldsById = array();

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\AbstractField $model
     */
    public function __construct(AbstractField $model)
    {
        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function boot()
    {
        if (is_null($this->collection)) {
            parent::boot();

            foreach ($this->collection as $field) {
                $this->fieldsByName[$field->field_name] = $field;
                $this->fieldsById[$field->field_id] = $field;
            }
        }
    }

    /**
     * Get the collection of Fields
     *
     * @return \rsanchez\Deep\Collection\AbstractFieldCollection
     */
    public function getFields()
    {
        $this->boot();

        return $this->collection;
    }

    /**
     * Get the field_id for the specified field name
     *
     * @param  string                     $field name of the field
     * @return \rsanchez\Deep\Model\Field
     */
    public function getFieldId($field)
    {
        $this->boot();

        return $this->fieldsByName[$field]->field_id;
    }

    /**
     * Get the field_id for the specified field name
     *
     * @param  int    $id id of the field
     * @return string
     */
    public function getFieldName($id)
    {
        $this->boot();

        return $this->fieldsById[$id]->field_name;
    }

    /**
     * Check if this collection has the specified field name
     *
     * @param  string  $field the name of the field
     * @return boolean
     */
    public function hasField($field)
    {
        $this->boot();

        return isset($this->fieldsByName[$field]);
    }

    /**
     * Check if this collection has the specified field id
     *
     * @param  int     $id the id of the field
     * @return boolean
     */
    public function hasFieldId($id)
    {
        $this->boot();

        return isset($this->fieldsById[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $this->boot();

        return isset($this->fieldsById[$id]) ? $this->fieldsById[$id] : null;
    }
}
