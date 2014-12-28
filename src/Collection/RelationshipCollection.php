<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\RelationshipEntry;

/**
 * Collection of \rsanchez\Deep\Model\RelationshipEntry
 */
class RelationshipCollection extends EntryCollection
{
    /**
     * Add a RelationshipEntry to this collection
     * @param  \rsanchez\Deep\Model\RelationshipEntry $item
     * @return void
     */
    public function add(RelationshipEntry $item)
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
