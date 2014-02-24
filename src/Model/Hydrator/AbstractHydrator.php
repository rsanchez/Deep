<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Entry;

abstract class AbstractHydrator implements HydratorInterface
{
    public function __construct(Collection $collection)
    {
    }

    public function preload(Collection $collection)
    {
    }

    abstract public function hydrate(Collection $collection, Entry $entry);
}
