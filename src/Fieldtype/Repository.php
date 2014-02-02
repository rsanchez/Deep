<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Fieldtype\Storage;
use rsanchez\Deep\Fieldtype\Factory;
use rsanchez\Deep\Fieldtype\Collection;

class Repository extends Collection
{
    public function __construct(Storage $storage, Factory $factory)
    {
        foreach ($storage() as $row) {

            $fieldtype = $factory->createFieldtype($row);

            $this->push($fieldtype);
        }
    }
}
