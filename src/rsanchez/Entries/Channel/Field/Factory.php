<?php

namespace rsanchez\Entries\Channel\Field;

use rsanchez\Entries\Channel\Field;
use \stdClass;

class Factory
{
    public function createField(stdClass $row)
    {
        return new Field($row);
    }
}
