<?php

namespace rsanchez\Deep\Entity\Field;

use rsanchez\Deep\Entity\Field\Collection;
use rsanchez\Deep\Common\Field\CollectionFactoryInterface;

class CollectionFactory implements CollectionFactoryInterface
{
    public function createCollection()
    {
        return new Collection();
    }
}
