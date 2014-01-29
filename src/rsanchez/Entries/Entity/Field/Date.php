<?php

namespace rsanchez\Entries\Entity\Field;

use rsanchez\Entries\Entity\Field;

class Date extends Field
{
    public function __invoke($format = 'U')
    {
        return $this->value ? date($format, $this->value) : null;
    }
}
