<?php

namespace rsanchez\Deep\Entity;

use rsanchez\Deep\Entity\Collection;

class CollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
