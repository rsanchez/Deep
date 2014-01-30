<?php

namespace rsanchez\Deep\FilePath;

use rsanchez\Deep\FilePath\FilePath;
use stdClass;

class Factory
{
    public function createFilePath(stdClass $row)
    {
        return new FilePath($row);
    }
}
