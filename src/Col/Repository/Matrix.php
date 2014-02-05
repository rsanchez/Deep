<?php

namespace rsanchez\Deep\Col\Repository;

use rsanchez\Deep\Col\Col;
use rsanchez\Deep\Col\Collection;
use rsanchez\Deep\Col\Storage\Matrix as Storage;
use rsanchez\Deep\Col\Factory;
use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Col\Repository\AbstractRepository;

class Matrix extends AbstractRepository
{
    public function __construct(Storage $storage, Factory $factory)
    {
        parent::__construct($storage, $factory);
    }
}
