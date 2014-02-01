<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;

class Date extends Fieldtype
{
    public function __invoke($value, $format = 'U')
    {
        return $value ? date($format, $value) : null;
    }
}
