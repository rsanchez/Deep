<?php

namespace rsanchez\Entries\Property;

use rsanchez\Entries\Property\AbstractProperty;
use stdClass;

interface FactoryInterface
{
    /**
     * Create a class that inherits AbstractProperty
     * @param  stdClass $row
     * @return rsanchez\Entries\Property\AbstractProperty
     */
    public function createProperty(stdClass $row);
}
