<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Entry;

interface HydratorInterface
{
    public function __construct(Collection $collection);

    public function preload(Collection $collection);

    public function hydrate(Collection $collection, Entry $entry);
}
