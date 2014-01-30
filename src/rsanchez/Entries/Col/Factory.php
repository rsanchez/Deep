<?php

namespace rsanchez\Entries\Col;

use rsanchez\Entries\Property\FactoryInterface as PropertyFactoryInterface;
use rsanchez\Entries\Col\Col;
use stdClass;

class Factory implements PropertyFactoryInterface
{
    /**
     * @inheritdoc
     * @return rsanchez\Entries\Col\Col
     */
    public function createProperty(stdClass $row)
    {
        return new Col($row);
    }
}
