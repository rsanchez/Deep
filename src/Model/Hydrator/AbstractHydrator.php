<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Collection;

abstract class AbstractHydrator implements HydratorInterface
{
    public function preload(Collection $collection)
    {
    }

    abstract public function hydrateCollection(Collection $collection);
}
