<?php

namespace rsanchez\Entries\Col;

use rsanchez\Entries\Property\Factory as PropertyFactory;
use rsanchez\Entries\Col;
use stdClass;

class Factory extends PropertyFactory
{
    public function createProperty(stdClass $row)
    {
        return new Col($row);
    }
}
