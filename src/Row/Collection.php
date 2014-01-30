<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Row\Row;
use rsanchez\Deep\Entity\Collection as EntityCollection;

class Collection extends EntityCollection
{
    public function push(Row $row)
    {
        parent::push($row);
    }
}
