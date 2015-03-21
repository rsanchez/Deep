<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Field;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\Field
 */
class FieldCollection extends AbstractFieldCollection
{
    /**
     * {@inheritdoc}
     */
    protected $modelClass = '\\rsanchez\\Deep\\Model\\Field';

    /**
     * Fieldtypes used by this collection
     * @var array
     */
    protected $fieldtypes = array();

    /**
     * Map of fieldtypes to field IDs:
     *    'fieldtype_name' => array(1, 2, 3),
     * @var array
     */
    protected $fieldIdsByFieldtype = array();

    protected $fieldsByFieldtype = array();

    /**
     * {@inheritdoc}
     */
    public function __construct(array $fields = array())
    {
        parent::__construct($fields);

        foreach ($fields as $field) {
            $this->addFieldtype($field->field_type);

            $this->fieldIdsByFieldtype[$field->field_type][] = $field->field_id;

            if (! isset($this->fieldsByFieldtype[$field->field_type])) {
                $this->fieldsByFieldtype[$field->field_type] = new FieldCollection();
            }

            $this->fieldsByFieldtype[$field->field_type]->items[] = $field;
        }
    }

    /**
     * Add a Field to this collection
     * @param  \rsanchez\Deep\Model\Field $field
     * @return void
     */
    protected function prepareModel(Model $item)
    {
        $field = $item;

        $this->addFieldtype($field->field_type);

        $this->fieldIdsByFieldtype[$field->field_type][] = $field->field_id;

        if (! isset($this->fieldsByFieldtype[$field->field_type])) {
            $this->fieldsByFieldtype[$field->field_type] = new FieldCollection();
        }

        $this->fieldsByFieldtype[$field->field_type]->items[] = $field;
    }

    /**
     * Check if this collection uses the specified fieldtype
     *
     * @param  string  $fieldtype
     * @return boolean
     */
    public function hasFieldtype($fieldtype)
    {
        return in_array($fieldtype, $this->fieldtypes);
    }

    /**
     * Get the field IDs for the specified fieldtype
     *
     * @param  string $fieldtype
     * @return array
     */
    public function getFieldIdsByFieldtype($fieldtype)
    {
        return isset($this->fieldIdsByFieldtype[$fieldtype]) ? $this->fieldIdsByFieldtype[$fieldtype] : array();
    }

    public function getFieldsByFieldtype($fieldtype)
    {
        return isset($this->fieldsByFieldtype[$fieldtype]) ? $this->fieldsByFieldtype[$fieldtype] : new FieldCollection();
    }

    /**
     * Get a list of names of fieldtypes in this collection
     * @return array
     */
    public function getFieldtypes()
    {
        return $this->fieldtypes;
    }

    /**
     * Add a fieldtype to the list of fieldtypes in this collection
     * @param  string $fieldtype
     * @return void
     */
    public function addFieldtype($fieldtype)
    {
        if (! in_array($fieldtype, $this->fieldtypes)) {
            $this->fieldtypes[] = $fieldtype;
        }
    }
}
