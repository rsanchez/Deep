<?php

namespace rsanchez\Deep\Channel\Field;

use rsanchez\Deep\Channel\Field\Collection;
use rsanchez\Deep\Common\Property\CollectionFactoryInterface;

class CollectionFactory implements CollectionFactoryInterface
{
    public function createCollection()
    {
        return new Collection();
    }
}
