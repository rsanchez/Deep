<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\PlayaEntry;
use rsanchez\Deep\Collection\PlayaCollection;

/**
 * Hydrator for the Playa fieldtype
 */
class PlayaHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\PlayaEntry
     */
    protected $model;

    /**
     * List of entries in this collection, organized by
     * type, entity and property
     * @var array
     */
    protected $entries;

    /**
     * Collection of entries being loaded by the parent collection
     * @var \rsanchez\Deep\Collection\PlayaCollection
     */
    protected $playaCollection;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param string                                     $fieldtype
     * @param \rsanchez\Deep\Model\PlayaEntry            $model
     */
    public function __construct(EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, PlayaEntry $model)
    {
        parent::__construct($collection, $hydrators, $fieldtype);

        $this->model = $model;

        $this->playaCollection = $this->model->parentEntryId($collection->modelKeys())->orderBy('rel_order')->get();

        foreach ($this->playaCollection as $entry) {
            $type = $entry->parent_row_id ? 'matrix' : 'entry';
            $entityId = $entry->parent_row_id ? $entry->parent_row_id : $entry->parent_entry_id;
            $propertyId = $entry->parent_row_id ? $entry->parent_col_id : $entry->parent_field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = array();
            }

            $this->entries[$type][$entityId][$propertyId][] = $entry;
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->playaCollection->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : array();

        return $this->playaCollection->createChildCollection($entries);
    }
}
