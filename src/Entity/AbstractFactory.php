<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;
use stdClass;

abstract class AbstractFactory
{
    public function createEntity(stdClass $row, PropertyCollection $propertyCollection)
    {
        return new Entity($row, $propertyCollection);
    }
}
