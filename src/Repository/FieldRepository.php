<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Repository;

use rsanchez\Deep\Collection\FieldCollection;
use rsanchez\Deep\Model\Field;

/**
 * Repository of all Fields
 */
class FieldRepository
{
    /**
     * Collection of all Fields
     * @var \rsanchez\Deep\Collection\FieldCollection
     */
    protected $collection;

    /**
     * Array of Field keyed by field_name
     * @var array
     */
    protected $fieldsByName = array();

    /**
     * Array of FieldCollection keyed by group_id
     * @var array
     */
    protected $fieldsByGroup = array();

    /**
     * Constructor
     *
     * @param \rsanchez\Deep\Model\Field $model
     */
    public function __construct(Field $model)
    {
        $this->collection = $model->all();

        foreach ($this->collection as $field) {
            $this->fieldsByName[$field->field_name] = $field;

            if (! array_key_exists($field->group_id, $this->fieldsByGroup)) {
                $this->fieldsByGroup[$field->group_id] = new FieldCollection();
            }

            $this->fieldsByGroup[$field->group_id]->push($field);
        }
    }

    /**
     * Get the field_id for the specified field name
     *
     * @param  string                     $field name of the field
     * @return \rsanchez\Deep\Model\Field
     */
    public function getFieldId($field)
    {
        return $this->fieldsByName[$field]->field_id;
    }

    /**
     * Check if this collection has the specified field name
     *
     * @param  string  $field the name of the field
     * @return boolean
     */
    public function hasField($field)
    {
        return array_key_exists($field, $this->fieldsByName);
    }

    /**
     * Get a Collection of fields from the specified group
     *
     * @param  int                                       $groupId
     * @return \rsanchez\Deep\Collection\FieldCollection
     */
    public function getFieldsByGroup($groupId)
    {
        return $groupId && isset($this->fieldsByGroup[$groupId]) ? $this->fieldsByGroup[$groupId] : new FieldCollection();
    }
}
