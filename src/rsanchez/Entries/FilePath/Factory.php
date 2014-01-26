<?php

namespace rsanchez\Entries\FilePath;

use rsanchez\Entries\FilePath;
use \stdClass;

class Factory
{
    public function createFilePath(stdClass $row)
    {
        return new FilePath($row);
    }
}
