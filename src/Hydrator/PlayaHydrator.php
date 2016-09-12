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
     * @param \rsanchez\Deep\Hydrator\HydratorCollection     $hydrators
     * @param string                                         $fieldtype
     * @param \rsanchez\Deep\Model\PlayaEntry                $model
     * @param \rsanchez\Deep\Collection\PlayaCollection|null $playaCollection
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, PlayaEntry $model, PlayaCollection $playaCollection = null)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->model = $model;

        $this->playaCollection = $playaCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function bootFromCollection(EntryCollection $collection)
    {
        $builder = $this->model->parentEntryId($collection->modelKeys())->orderBy('rel_order');

        if (!$this->childHydrationEnabled) {
            $builder = $this->castToDeepBuilder($builder)->setHydrationDisabled();
        }

        $this->playaCollection = $builder->get();

        foreach ($this->playaCollection as $entry) {
            $type = $entry->parent_row_id ? 'matrix' : 'entry';
            $entityId = $entry->parent_row_id ? $entry->parent_row_id : $entry->parent_entry_id;
            $propertyId = $entry->parent_row_id ? $entry->parent_col_id : $entry->parent_field_id;

            if (! isset($this->entries[$type][$entityId][$propertyId])) {
                $this->entries[$type][$entityId][$propertyId] = [];
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
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        if (! isset($this->playaCollection)) {
            return new PlayaCollection();
        }

        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : [];

        return $this->playaCollection->createChildCollection($entries);
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Collection\PlayaCollection|array|null $value
     * @return \rsanchez\Deep\Collection\PlayaCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof PlayaCollection) {
            return $value;
        }

        // array of entry ids
        if (is_array($value)) {
            $collection = new PlayaCollection();

            $collection->addEntryIds($value);

            return $collection;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\PlayaCollection.');
    }
}
