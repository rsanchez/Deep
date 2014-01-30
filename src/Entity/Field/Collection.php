<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Entity\Field;
use IteratorAggregate;
use ArrayIterator;

class Collection implements IteratorAggregate
{
    protected $fields = array();
    protected $fieldsByName = array();
    protected $fieldsById = array();

    /**
     * A shortcut to the field by name
     * @param  string $name the field_name attribute of the field
     * @return Field;
     */
    public function __get($name)
    {
        if (isset($this->fieldsByName[$name])) {
            return $this->fieldsByName[$name];
        }

        throw new \Exception('invalid field name');//@TODO
    }

    public function find($id)
    {
        if (is_numeric($id)) {
            //@TODO custom exception
            if (! array_key_exists($id, $this->fieldsById)) {
                throw new \Exception('invalid field id');
            }

            return $this->fieldsById[$id];
        }

        if (! array_key_exists($id, $this->fieldsByName)) {
            throw new \Exception('invalid field name');
        }

        return $this->fieldsByName[$id];
    }

    public function filterByType($type)
    {
        $fields = array_filter($this->fields, function ($field) use ($type) {
            return $field->type() === $type;
        });

        $collection = new Collection();

        array_walk($fields, array($collection, 'push'));

        return $collection;
    }

    public function fieldIds()
    {
        return array_keys($this->fieldsById);
    }

    public function push(Field $field)
    {
        array_push($this->fields, $field);

        $this->fieldsById[$field->id()] =& $field;
        $this->fieldsByName[$field->name()] =& $field;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }
}
