<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\Entry\Field\Collection;

class CollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
