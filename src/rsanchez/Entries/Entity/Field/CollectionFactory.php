<?php

namespace rsanchez\Entries\Entity\Field;

use rsanchez\Entries\Entity\Field\Collection;

class CollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
