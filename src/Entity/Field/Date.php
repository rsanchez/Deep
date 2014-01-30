<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Entity\Field;

class Date extends Field
{
    public function __invoke($format = 'U')
    {
        return $this->value ? date($format, $this->value) : null;
    }
}
