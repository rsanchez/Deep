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
use rsanchez\Deep\Collection\RelationshipCollection;
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
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

        $this->relationshipCollection = RelationshipEntry::siblings($collection->modelKeys())->get();

        foreach ($this->relationshipCollection as $entry) {
            if (! isset($this->entries[$entry->sibling_id])) {
                $this->entries[$entry->sibling_id] = array();
            }

            $this->entries[$entry->sibling_id][] = $entry;
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->relationshipCollection->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = isset($this->entries[$entity->getId()]) ? $this->entries[$entity->getId()] : array();

        $value = $this->relationshipCollection->createChildCollection($entries);

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
