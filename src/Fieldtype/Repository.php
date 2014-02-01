<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Fieldtype\Storage;
use rsanchez\Deep\Fieldtype\Factory;
use IteratorAggregate;

class Repository implements IteratorAggregate
{
    private $fieldtypes = array();
    private $fieldtypesByName = array();

    public function __construct(Storage $storage, Factory $factory)
    {
        foreach ($storage() as $row) {

            $fieldtype = $factory->createFieldtype($row);

            $this->push($fieldtype);
        }
    }

    public function push(Fieldtype $fieldtype)
    {
        array_push($this->fieldtypes, $fieldtype);
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
        return new ArrayIterator($this->fieldtype);
    }
}
