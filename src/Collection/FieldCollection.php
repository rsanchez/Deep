<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Collection\AbstractFieldCollection;

/**
 * Collection of \rsanchez\Deep\Model\Field
 */
class FieldCollection extends AbstractFieldCollection
{
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

    /**
     * {@inheritdoc}
     */
    public function __construct(array $fields = array())
    {
        parent::__construct($fields);

        foreach ($fields as $field) {
            $this->addFieldtype($field->field_type);

            $this->fieldIdsByFieldtype[$field->field_type][] = $field->field_id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push($field)
    {
        $this->addFieldtype($field->field_type);

        $this->fieldIdsByFieldtype[$field->field_type][] = $field->field_id;

        return parent::push($field);
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
