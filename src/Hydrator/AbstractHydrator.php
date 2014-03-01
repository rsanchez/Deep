<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;

abstract class AbstractHydrator implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        // load from external DBs here
    }

    /**
     * {@inheritdoc}
     */
    abstract public function hydrate(Entry $entry);
}
