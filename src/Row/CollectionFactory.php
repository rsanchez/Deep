<?php

namespace rsanchez\Deep\Row;

use rsanchez\Deep\Entity\CollectionFactory as EntityCollectionFactory;
use rsanchez\Deep\Row\Collection;

class CollectionFactory extends EntityCollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
