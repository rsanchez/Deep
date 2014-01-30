<?php

namespace rsanchez\Deep\Property;

use rsanchez\Deep\Property\AbstractProperty;
use stdClass;

interface FactoryInterface
{
    /**
     * Create a class that inherits AbstractProperty
     * @param  stdClass $row
     * @return rsanchez\Deep\Property\AbstractProperty
     */
    public function createProperty(stdClass $row);
}
