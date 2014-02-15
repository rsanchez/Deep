<?php

namespace rsanchez\Deep\Col;

use rsanchez\Deep\Col\Collection;
use rsanchez\Deep\Common\Property\CollectionFactoryInterface;

class CollectionFactory implements CollectionFactoryInterface
{
    public function createCollection()
    {
        return new Collection();
    }
}
