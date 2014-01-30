<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Entity\Field\Collection;

class CollectionFactory
{
    public function createCollection()
    {
        return new Collection();
    }
}
