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
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Hydrator\AbstractHydrator;
use rsanchez\Deep\Model\PlayaEntry;

/**
 * Hydrator for the Playa fieldtype
 */
class PlayaHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $entries = PlayaEntry::parentEntryId($collection->modelKeys())->get();

        foreach ($entries as $entry) {
            $type = $entry->parent_row_id ? 'matrix' : 'entry';
            $entityId = $entry->parent_row_id ? $entry->parent_row_id : $entry->parent_entry_id;
            $propertyId = $entry->parent_row_id ? $entry->parent_col_id : $entry->parent_field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = new PlayaCollection();
            }

            $this->entries[$type][$entityId][$propertyId]->push($asset);
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($entries->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : new PlayaCollection();

        $entity->setAttribute($property->getName(), $value);
    }
}
