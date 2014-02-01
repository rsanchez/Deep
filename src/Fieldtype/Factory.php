<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use stdClass;

class Factory
{
    public function createFieldtype(stdClass $row)
    {
        return new Fieldtype($row);
    }
}
