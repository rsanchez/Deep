<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Property\FactoryInterface as PropertyFactoryInterface;
use rsanchez\Deep\Channel\Field;
use stdClass;

class Factory implements PropertyFactoryInterface
{
    /**
     * @inheritdoc
     * @return rsanchez\Deep\Channel\Field
     */
    public function createProperty(stdClass $row)
    {
        return new Field($row);
    }
}
