<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Property\FactoryInterface as PropertyFactoryInterface;
use rsanchez\Entries\Channel\Field;
use stdClass;

class Factory implements PropertyFactoryInterface
{
    /**
     * @inheritdoc
     * @return rsanchez\Entries\Channel\Field
     */
    public function createProperty(stdClass $row)
    {
        return new Field($row);
    }
}
