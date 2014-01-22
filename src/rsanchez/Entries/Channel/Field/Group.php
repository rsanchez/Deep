<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use \IteratorAggregate;
use \ArrayIterator;

class Group implements IteratorAggregate
{
    private $fields = array();

    public function push(Field $field)
    {
        array_push($this->fields, $field);

        $this->{$field->field_name} =& $field;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }
}
