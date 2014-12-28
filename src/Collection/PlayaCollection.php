<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Collection\EntryCollection;

/**
 * Collection of \rsanchez\Deep\Model\PlayaEntry
 */
class PlayaCollection extends EntryCollection
{
    /**
     * Add a PlayaEntry to this collection
     * @param  \rsanchez\Deep\Model\PlayaEntry $item
     * @return void
     */
    public function add(PlayaEntry $item)
    {
        parent::add($item);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        // flatten the array keys
        return array_values(parent::toArray());
    }
}
