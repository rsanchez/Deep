<?php

namespace rsanchez\Deep\Common\Property;

interface CollectionFactoryInterface
{
    /**
     * @return rsanchez\Deep\Common\Property\AbstractCollection
     */
    public function createCollection();
}
