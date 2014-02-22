<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Collection;

interface HydratorInterface
{
    public function hydrateCollection(Collection $collection);

    public function getFieldtype();
}
