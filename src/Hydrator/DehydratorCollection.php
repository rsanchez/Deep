<?php

namespace rsanchez\Deep\Hydrator;

use Illuminate\Support\Collection;

/**
 * Collection of Dehydrators
 */
class DehydratorCollection extends Collection
{
    /**
     * Throw an exception if attempting to push a Hydrator without specifying type
     */
    public function push($value)
    {
        throw new \Exception('Deydrators must have a key. Use put method instead.');
    }

    /**
     * Add a Hydrator to the collection
     *
     * @param  string                                     $type
     * @param  \rsanchez\Deep\Hydrator\AbstractDehydrator $hydrator
     * @return void
     */
    public function put($type, $hydrator)
    {
        return $this->addDehydrator($type, $hydrator);
    }

    /**
     * Add a Hydrator to the collection
     *
     * @param  string                                   $type
     * @param  \rsanchez\Deep\Hydrator\AbstractDehydrator $hydrator
     * @return void
     */
    public function addDehydrator($type, AbstractDehydrator $hydrator)
    {
        $this->items[$type] = $hydrator;
    }
}
