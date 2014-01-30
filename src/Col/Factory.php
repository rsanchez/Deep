<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Property\FactoryInterface as PropertyFactoryInterface;
use rsanchez\Deep\Col\Col;
use stdClass;

class Factory implements PropertyFactoryInterface
{
    /**
     * @inheritdoc
     * @return rsanchez\Deep\Col\Col
     */
    public function createProperty(stdClass $row)
    {
        return new Col($row);
    }
}
