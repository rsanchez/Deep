<?php

namespace rsanchez\Deep\Common\Entity;

use rsanchez\Deep\Common\Property\AbstractCollection as PropertyCollection;
use stdClass;

abstract class AbstractFactory
{
    public function createEntity(stdClass $row, PropertyCollection $propertyCollection)
    {
        return new Entity($row, $propertyCollection);
    }
}
