<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;
use stdClass;

abstract class AbstractEntity
{
    public $fieldData = array();

    protected $methodAliases = array();

    public function __construct(stdClass $row, PropertyCollection $propertyCollection)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        $this->fields = $propertyCollection;

        foreach ($propertyCollection as $property) {
            $column = $property->inputName();
            $value = property_exists($row, $column) ? $row->$column : '';
            $this->fieldData[$property->name()] = $value;
        }
    }

    abstract public function id();

    public function toArray()
    {
        return (array) $this;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->fieldData)) {
            $value = $this->fieldData[$name];

            try {
                $field = $this->fields->find($name);

                // set the property so we don't call this function again
                return $this->{$name} = $field->fieldtype($value);
            } catch (\Exception $e) {
                //$e->getMessage();
            }

            return $this->{$name} = $value;
        }
    }

    public function __call($name, $args)
    {
        if (array_key_exists($name, $this->methodAliases)) {
            return call_user_func_array(array($this, $this->methodAliases[$name]), $args);
        }

        /*
        if (array_key_exists($name, $this->fields)) {
            return call_user_func_array(array($this->fields[$name], '__invoke'), $args);
        }
        */
    }
}
