<?php

namespace rsanchez\Entries;

use rsanchez\Entries\Property;
use \IteratorAggregate;
use \ArrayIterator;

class PropertyCollection implements IteratorAggregate
{
    protected $properties = array();
    protected $propertiesByName = array();
    protected $propertiesById = array();

    /**
     * A shortcut to the field by name
     * @param  string $name the field_name attribute of the field
     * @return Field;
     */
    public function __get($name)
    {
        if (isset($this->propertiesByName[$name])) {
            return $this->propertiesByName[$name];
        }

        throw new \Exception('invalid field name');//@TODO
    }

    public function find($id)
    {
        if (is_numeric($id)) {
            //@TODO custom exception
            if (! array_key_exists($id, $this->propertiesById)) {
                throw new \Exception('invalid field id');
            }

            return $this->propertiesById[$id];
        }

        if (! array_key_exists($id, $this->propertiesByName)) {
            throw new \Exception('invalid field name');
        }

        return $this->propertiesByName[$id];
    }

    public function filterByType($type)
    {
        $properties = array_filter($this->properties, function ($field) use ($type) {
            return $field->type() === $type;
        });

        $collection = new PropertyCollection();

        array_walk($properties, array($collection, 'push'));

        return $collection;
    }

    public function fieldIds()
    {
        return array_keys($this->propertiesById);
    }

    public function push(Property $field)
    {
        array_push($this->properties, $field);

        $this->propertiesById[$field->field_id] =& $field;
        $this->propertiesByName[$field->field_name] =& $field;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }
}
