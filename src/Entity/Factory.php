<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Property\AbstractCollection as PropertyCollection;

class Factory
{
    public function createEntity(stdClass $row, PropertyCollection $propertyCollection)
    {
        return new Entity($row, $propertyCollection);
    }
}
