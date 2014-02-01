<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Entity\Field\Field;
use rsanchez\Deep\Entity\Field\Factory as FieldFactory;
use rsanchez\Deep\Entity\Field\CollectionFactory as FieldCollectionFactory;
use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;
use stdClass;

class Entity
{
    public $fields;

    protected $methodAliases = array();

    public function __construct(stdClass $row, PropertyCollection $propertyCollection, FieldFactory $fieldFactory, FieldCollectionFactory $fieldCollectionFactory)
    {
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }

        $this->fields = $fieldCollectionFactory->createCollection();

        foreach ($propertyCollection as $property) {
            $column = $property->prefix().$property->id();
            $value = property_exists($row, $column) ? $row->$column : '';
            $field = $fieldFactory->createField($value, $property);
            $this->fields->push($field);
            $this->{$property->name()} = $field;
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
