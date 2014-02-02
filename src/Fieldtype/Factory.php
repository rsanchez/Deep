<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use stdClass;

class Factory
{
    public function createFieldtype(stdClass $row, $closure = null)
    {
        $fieldtype = null;

        if (is_callable($closure)) {
            $fieldtype = call_user_func($closure, $row);
        }

        if (is_string($closure) && class_exists($closure)) {
            $fieldtype = new $closure($row);
        }

        if ($fieldtype !== null) {
            if (! $fieldtype instanceof Fieldtype) {
                throw new \Exception('class must extend Fieldtype');//@TODO real exception
            }

            return $fieldtype;
        }

        return new Fieldtype($row);
    }
}
