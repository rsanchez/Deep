<?php

namespace rsanchez\Entries\Property;

use rsanchez\Entries\Property;
use stdClass;

class Factory
{
    public function createProperty(stdClass $row)
    {
        return new Property($row);
    }
}
