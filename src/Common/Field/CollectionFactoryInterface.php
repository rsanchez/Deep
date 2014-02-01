<?php

namespace rsanchez\Deep\Common\Field;

interface CollectionFactoryInterface
{
    /**
     * @return rsanchez\Deep\Common\Field\AbstractCollection
     */
    public function createCollection();
}
