<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Fieldtype\Storage;
use rsanchez\Deep\Fieldtype\Factory;
use rsanchez\Deep\Fieldtype\Collection;

class Repository extends Collection
{
    protected $registeredFieldtypes = array();

    /**
     * Register new fieldtypes that can be instantiated by this factory
     * @param  string   $type    the short name of the fieldtype (eg. matrix)
     * @param  callable $closure a closure that returns rsanchez\Deep\Fieldtype\Fieldtype or descendant
     * @return void
     */
    public function registerFieldtype($type, $closure)
    {
        $this->registeredFieldtypes[$type] = $closure;
    }

    private function initialize()
    {
        static $initialized = false;

        if ($initialized) {
            return;
        }

        foreach (call_user_func($this->storage) as $row) {

            $closure = null;

            if (isset($this->registeredFieldtypes[$row->name])) {
                $closure = $this->registeredFieldtypes[$row->name];
            }

            $fieldtype = $this->factory->createFieldtype($row, $closure);

            $this->attach($fieldtype);
        }

        $initialized = true;
    }

    public function find($name)
    {
        $this->initialize();

        return parent::find($name);
    }

    public function valid()
    {
        $this->initialize();

        return parent::valid();
    }

    public function rewind()
    {
        $this->initialize();

        return parent::rewind();
    }

    public function __construct(Storage $storage, Factory $factory)
    {
        $this->storage = $storage;
        $this->factory = $factory;
    }
}
