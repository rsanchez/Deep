<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use Closure;
use stdClass;

class Factory
{
    protected $registeredFieldtypes = array();

    /**
     * Register new fieldtypes that can be instantiated by this factory
     * @param  string  $type    the short name of the fieldtype (eg. matrix)
     * @param  Closure $closure a closure that returns rsanchez\Deep\Fieldtype\Fieldtype or descendant
     * @return void
     */
    public function registerFieldtype($type, Closure $closure)
    {
        $this->registeredFieldtypes[$type] = $closure;
    }

    public function createFieldtype(stdClass $row)
    {
        if (isset($this->registeredFieldtypes[$row->name])) {
            return call_user_func($this->registeredFieldtypes[$row->name], $row);
        }

        return new Fieldtype($row);
    }
}
