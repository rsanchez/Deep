<?php

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;

interface HydratorInterface
{
    public function __construct(EntryCollection $collection);

    public function preload(array $entryIds);

    public function hydrate(Entry $entry);
}
