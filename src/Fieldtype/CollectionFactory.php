<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Collection;

class CollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
