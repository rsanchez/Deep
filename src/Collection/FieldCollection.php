<?php

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

class FieldCollection extends Collection
{
    protected $fieldsByName = array();

    public function __construct(array $fields = array())
    {
        foreach ($fields as $field) {
            $this->fieldsByName[$field->field_name] = $field;
        }

        parent::__construct($fields);
    }

    public function getFieldId($field)
    {
        return $this->fieldsByName[$field]->field_id;
    }

    public function hasField($field)
    {
        return array_key_exists($field, $this->fieldsByName);
    }
}
