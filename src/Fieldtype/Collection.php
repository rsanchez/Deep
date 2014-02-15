<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use SplObjectStorage;

class Collection extends SplObjectStorage
{
    protected $fieldtypesByName = array();

    public function attach(Fieldtype $fieldtype)
    {
        $this->fieldtypesByName[$fieldtype->name] =& $fieldtype;

        return parent::attach($fieldtype);
    }

    public function unshift(Fieldtype $fieldtype)
    {
        $fieldtypes = new SplObjectStorage();
        $fieldtypes->attach($fieldtype);
        $fieldtypes->addAll($this);
        $this->removeAll($this);
        $this->addAll($fieldtypes);

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
}
