<?php

namespace rsanchez\Deep\Entry\Field;

use rsanchez\Deep\Entity\Field\CollectionFactory as EntityFieldCollectionFactory;
use rsanchez\Deep\Entry\Field\Collection;

class CollectionFactory extends EntityFieldCollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
