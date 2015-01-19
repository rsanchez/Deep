<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\RelationshipCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\RelationshipEntry;

/**
 * Hydrator for the Relationship fieldtype
 */
class RelationshipHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\RelationshipEntry
     */
    protected $model;

    /**
     * Collection of entries being loaded by the parent collection
     * @var \rsanchez\Deep\Collection\RelationshipCollection
     */
    protected $relationshipCollection;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Hydrator\HydratorCollection       $hydrators
     * @param string                                           $fieldtype
     * @param \rsanchez\Deep\Model\RelationshipEntry           $model
     * @param \rsanchez\Deep\Model\RelationshipCollection|null $relationshipCollection
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, RelationshipEntry $model, RelationshipCollection $relationshipCollection = null)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->model = $model;

        $this->relationshipCollection = $relationshipCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function bootFromCollection(EntryCollection $collection)
    {
        $this->relationshipCollection = $this->model->parentEntryId($collection->modelKeys())->orderBy('order')->get();

        foreach ($this->relationshipCollection as $entry) {
            $type = $entry->grid_field_id ? 'grid' : 'entry';
            $entityId = $entry->grid_field_id ? $entry->grid_row_id : $entry->parent_id;
            $propertyId = $entry->grid_field_id ? $entry->grid_col_id : $entry->field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = array();
            }

            $this->entries[$type][$entityId][$propertyId][] = $entry;
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->relationshipCollection->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        if (! isset($this->relationshipCollection)) {
            return new RelationshipCollection();
        }

        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : [];

        return $this->relationshipCollection->createChildCollection($entries);
    }
}
