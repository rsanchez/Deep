<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\PlayaEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\PlayaEntry
 */
class PlayaCollection extends EntryCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addPlayaEntry($item);
    }

    /**
     * Add a PlayaEntry to this collection
     * @param  \rsanchez\Deep\Model\PlayaEntry $item
     * @return void
     */
    public function addPlayaEntry(PlayaEntry $item)
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

    /**
     * Add an entry based on ID
     * @param $entryId
     */
    public function addEntryId($entryId)
    {
        $this->push(PlayaEntry::find($entryId));
    }

    /**
     * Add several entries based on ID
     * @param $entryIds
     */
    public function addEntryIds($entryIds)
    {
        $this->items += PlayaEntry::entryId($entryIds)->get()->all();
    }
}
