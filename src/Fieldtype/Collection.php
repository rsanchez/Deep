<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use IteratorAggregate;
use ArrayIterator;

class Collection implements IteratorAggregate
{
    protected $fieldtypes = array();
    protected $fieldtypesByName = array();

    public function attach(Fieldtype $fieldtype)
    {
        array_push($this->fieldtypes, $fieldtype);
        $this->fieldtypesByName[$fieldtype->name] =& $fieldtype;
    }

    public function unshift(Fieldtype $fieldtype)
    {
        array_unshift($this->fieldtypes, $fieldtype);
        $this->fieldtypesByName[$fieldtype->name] =& $fieldtype;
    }

    public function find($name)
    {
        //@TODO custom exception
        if (! array_key_exists($name, $this->fieldtypesByName)) {
            throw new \Exception('invalid fieldtype name');
        }

        return $this->fieldtypesByName[$name];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fieldtypes);
    }
}
