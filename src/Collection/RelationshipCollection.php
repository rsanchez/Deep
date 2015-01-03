<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\RelationshipEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\RelationshipEntry
 */
class RelationshipCollection extends EntryCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addRelationshipEntry($item);
    }

    /**
     * Add a RelationshipEntry to this collection
     * @param  \rsanchez\Deep\Model\RelationshipEntry $item
     * @return void
     */
    public function addRelationshipEntry(RelationshipEntry $item)
    {
        $this->addEntry($item);
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
