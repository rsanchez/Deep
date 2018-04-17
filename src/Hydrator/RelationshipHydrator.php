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
        $builder = $this->model->parentEntryId($collection->modelKeys())->orderBy('order');

        if (!$this->childHydrationEnabled) {
            $builder = $this->castToDeepBuilder($builder)->setHydrationDisabled();
        }

        $this->relationshipCollection = $builder->get();

        foreach ($this->relationshipCollection as $entry) {
            $type = $entry->grid_field_id ? 'grid' : 'entry';
            $entityId = $entry->grid_field_id ? $entry->grid_row_id : $entry->parent_id;
            $propertyId = $entry->grid_field_id ? $entry->grid_col_id : $entry->field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = [];
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
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        if (! isset($this->relationshipCollection)) {
            return new RelationshipCollection();
        }

        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : [];

        return $this->relationshipCollection->createChildCollection($entries);
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Collection\RelationshipCollection|array|null $value
     * @return \rsanchez\Deep\Collection\RelationshipCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof RelationshipCollection) {
            return $value;
        }

        // array of entry ids
        if (is_array($value)) {
            $collection = new RelationshipCollection();

            $collection->addEntryIds($value);

            return $collection;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\RelationshipCollection.');
    }
}
