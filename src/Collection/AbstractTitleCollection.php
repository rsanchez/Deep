<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Collection\AbstractFilterableCollection;

/**
 * Collection of \rsanchez\Deep\Model\Title
 */
abstract class AbstractTitleCollection extends AbstractFilterableCollection
{
    /**
     * All of the entry IDs in this collection (including related entries)
     * @var array
     */
    protected $entryIds = array();

    /**
     * Channels used by this collection
     * @var \rsanchez\Deep\Collection\ChannelCollection
     */
    public $channels;

    /**
     * Get all the entry Ids from this collection.
     * This includes both the entries directly in this collection,
     * and entries found in Playa/Relationship fields
     *
     * @return array
     */
    public function getEntryIds()
    {
        return $this->entryIds;
    }

    /**
     * Add additional entry ids to this collection
     *
     * @param array $entryIds
     */
    public function addEntryIds(array $entryIds)
    {
        $this->entryIds = array_unique(array_merge($this->entryIds, $entryIds));
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        if (func_num_args() === 0) {
            $options = JSON_NUMERIC_CHECK;
        }

        return parent::toJson($options);
    }
}
