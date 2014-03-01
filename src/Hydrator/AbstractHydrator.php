<?php

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;

abstract class AbstractHydrator implements HydratorInterface
{
    public function __construct(EntryCollection $collection)
    {
        $this->collection = $collection;
    }

    public function preload(array $entryIds)
    {
        // load from external DBs here
    }

    abstract public function hydrate(Entry $entry);
}
