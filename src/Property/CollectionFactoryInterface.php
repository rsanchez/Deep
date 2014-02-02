<?php

namespace rsanchez\Deep\Property;

interface CollectionFactoryInterface
{
    /**
     * @return rsanchez\Deep\Property\AbstractCollection
     */
    public function createCollection();
}
