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
 * Hydrator for the Parent Relationships
 */
class ParentsHydrator extends AbstractHydrator
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
        $builder = $this->model->parents($collection->modelKeys());

        if (!$this->childHydrationEnabled) {
            $builder = $this->castToDeepBuilder($builder)->setHydrationDisabled();
        }

        $this->relationshipCollection = $builder->get();

        foreach ($this->relationshipCollection as $entry) {
            if (! isset($this->entries[$entry->child_id])) {
                $this->entries[$entry->child_id] = [];
            }

            $this->entries[$entry->child_id][] = $entry;
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

        $entries = isset($this->entries[$entity->getId()]) ? $this->entries[$entity->getId()] : [];

        return $this->relationshipCollection->createChildCollection($entries);
    }
}
