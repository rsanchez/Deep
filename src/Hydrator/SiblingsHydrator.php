<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\Entry;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\RelationshipEntry;

/**
 * Hydrator for the Sibling Relationships
 */
class SiblingsHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $this->entries = RelationshipEntry::siblings($collection->modelKeys())->get();

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->entries->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(Entry $entry)
    {
        $entry->setAttribute($this->fieldtype, $this->entries->filter(function ($siblingEntry) use ($entry) {
            return $entry->getKey() === $siblingEntry->sibling_id && $entry->getKey() !== $siblingEntry->getKey();
        }));
    }
}
