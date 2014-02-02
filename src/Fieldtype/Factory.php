<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use stdClass;

class Factory
{
    protected $registeredFieldtypes = array();

    /**
     * Register new fieldtypes that can be instantiated by this factory
     * @param  string  $type    the short name of the fieldtype (eg. matrix)
     * @param  callable $closure a closure that returns rsanchez\Deep\Fieldtype\Fieldtype or descendant
     * @return void
     */
    public function registerFieldtype($type, $closure)
    {
        $this->registeredFieldtypes[$type] = $closure;
    }

    public function createFieldtype(stdClass $row)
    {
        if (isset($this->registeredFieldtypes[$row->name])) {
            $closure = $this->registeredFieldtypes[$row->name];
            $fieldtype = null;

            if (is_callable($closure)) {
                $fieldtype = call_user_func($this->registeredFieldtypes[$row->name], $row);
            }

            if (is_string($closure) && class_exists($closure)) {
                $fieldtype = new $closure($row);
            }

            if (! $fieldtype instanceof Fieldtype) {
                throw new \Exception('class must extend Fieldtype');//@TODO real exception
            }

            return $fieldtype;
        }

        return new Fieldtype($row);
    }
}
