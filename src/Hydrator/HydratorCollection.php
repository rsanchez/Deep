<?php

namespace rsanchez\Deep\Hydrator;

use Illuminate\Support\Collection;

class HydratorCollection extends Collection
{
    public function push($value)
    {
        throw new \Exception('Hydrators must have a key. Use ->put instead.');
    }

    public function put($type, AbstractHydrator $hydrator)
    {
        return parent::put($type, $hydrator);
    }
}
