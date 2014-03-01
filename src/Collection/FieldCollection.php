<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

class FieldCollection extends Collection
{
    /**
     * array of field_name => \rsanchez\Deep\Model\Field
     * @var array
     */
    protected $fieldsByName = array();

    public function __construct(array $fields = array())
    {
        foreach ($fields as $field) {
            $this->fieldsByName[$field->field_name] = $field;
        }

        parent::__construct($fields);
    }

    /**
     * Get the field_id for the specified field name
     *
     * @param  string $field name of the field
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
}
