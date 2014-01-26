<?php

namespace rsanchez\Entries;

use \stdClass;

class FilePath
{
    public $id;
    public $server_path;
    public $url;

    public function __construct(stdClass $row)
    {
        $properties = get_class_vars(__CLASS__);

        foreach ($properties as $property => $value) {
            if (property_exists($row, $property)) {
                $this->$property = $row->$property;
            }
        }
    }
}
