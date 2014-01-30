<?php

namespace rsanchez\Entries\Entity;

use rsanchez\Entries\Entity\Field;
use rsanchez\Entries\Entity\Field\Collection as FieldCollection;
use stdClass;

class Entity
{
    public $fields;

    protected $methodAliases = array();

    public function __construct(stdClass $row, FieldCollection $fieldCollection)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        $this->fields = $fieldCollection;

        foreach ($this->fields as $field) {
            $field->setEntity($this);
            $this->{$field->name()} = $field;
        }
    }

    public function toArray()
    {
        return (array) $this;
    }

    public function __call($name, $args)
    {
        /*
        if (isset($this->$name) && $this->$name instanceof Field) {
            return call_user_func_array($this->$name, $args);
        }
        */
        if (array_key_exists($name, $this->methodAliases)) {
            return call_user_func_array(array($this, $this->methodAliases[$name]), $args);
        }

        if (array_key_exists($name, $this->fields)) {
            return call_user_func_array(array($this->fields[$name], '__invoke'), $args);
        }
    }
}
