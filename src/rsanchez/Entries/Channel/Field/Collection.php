<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use \IteratorAggregate;
use \ArrayIterator;

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

    public function push(Field $field)
    {
        array_push($this->fields, $field);

        $this->fieldsById[$field->field_id] =& $field;
        $this->fieldsByName[$field->field_name] =& $field;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }
}
