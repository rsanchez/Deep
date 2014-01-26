<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\Entry\Field;

class Date extends Field
{
    public function __invoke($format = 'U')
    {
        return $this->value ? date($format, $this->value) : null;
    }
}
