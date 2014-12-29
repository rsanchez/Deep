<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionInterface;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\RelationshipCollection;
use rsanchez\Deep\Model\AbstractProperty;
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
     * {@inheritdoc}
     *
     * @param \Illuminate\Database\ConnectionInterface   $db
     * @param \rsanchez\Deep\Collection\EntryCollection  $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection $hydrators
     * @param string                                     $fieldtype
     * @param \rsanchez\Deep\Model\RelationshipEntry     $model
     */
    public function __construct(ConnectionInterface $db, EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, RelationshipEntry $model)
    {
        parent::__construct($db, $collection, $hydrators, $fieldtype);

        $this->model = $model;

        $this->relationshipCollection = $this->model->parents($collection->modelKeys())->get();

        foreach ($this->relationshipCollection as $entry) {
            if (! isset($this->entries[$entry->child_id])) {
                $this->entries[$entry->child_id] = array();
            }

            $this->entries[$entry->child_id][] = $entry;
        }

        // add these entry IDs to the main collection
        $collection->addEntryIds($this->relationshipCollection->modelKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $entries = isset($this->entries[$entity->getId()]) ? $this->entries[$entity->getId()] : array();

        $value = $this->relationshipCollection->createChildCollection($entries);

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }
}
