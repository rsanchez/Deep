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
 * Hydrator for the Parent Relationships
 */
class ParentsHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function __construct(EntryCollection $collection, $fieldtype)
    {
        parent::__construct($collection, $fieldtype);

        $entries = RelationshipEntry::parents($collection->modelKeys())->get();

        foreach ($entries as $entry) {
            if (! isset($this->entries[$entry->child_id])) {
                $this->entries[$entry->child_id] = new RelationshipCollection();
            }

            $this->entries[$entry->child_id]->push($entry);
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($entries->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $value = isset($this->entries[$entity->getId()]) ? $this->entries[$entity->getId()] : new RelationshipCollection();

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
