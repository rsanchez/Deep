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
 * Hydrator for the Relationship fieldtype
 */
class RelationshipHydrator extends AbstractHydrator
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
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $entries = isset($this->entries[$entity->getType()][$entity->getId()][$property->getId()])
            ? $this->entries[$entity->getType()][$entity->getId()][$property->getId()] : array();

        $value = $this->relationshipCollection->createChildCollection($entries);

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, AbstractProperty $property, AbstractEntity $parentEntity = null, AbstractProperty $parentProperty = null)
    {
        $entries = $entity->getAttribute($property->getName());

        // drop old relations
        $query = $this->db->table('relationships');

        if ($parentEntity && $parentProperty) {
            $query->where('parent_id', $parentEntity->getId())
                ->where($entity->getPrefix().'_'.$parentProperty->getPrefix().'_id', $parentProperty->getId())//grid_field_id
                ->where($entity->getPrefix().'_'.$property->getPrefix().'_id', $property->getId())//grid_col_id
                ->where($entity->getPrefix().'_'.$entity->getPrefix().'_id', $entity->getId());//grid_row_id
        } else {
            $query->where('parent_id', $entity->getId())
                ->where($property->getPrefix().'_id', $property->getId());//field_id
        }

        $query->delete();

        if ($entries) {
            foreach ($entries as $i => $entry) {
                $entry->save();

                $data = [
                    'child_id' => $entry->entry_id,
                    'order' => $i,
                ];

                if ($parentEntity && $parentProperty) {
                    $data['parent_id'] = $parentEntity->getId();
                    $data[$entity->getPrefix().'_'.$parentProperty->getPrefix().'_id'] = $parentProperty->getId();//grid_field_id
                    $data[$entity->getPrefix().'_'.$property->getPrefix().'_id'] = $property->getId();//grid_col_id
                    $data[$entity->getPrefix().'_'.$entity->getPrefix().'_id'] = $entity->getId();//grid_row_id
                } else {
                    $data['parent_id'] = $entity->getId();
                    $data[$property->getPrefix().'_id'] = $property->getId();
                }

                $this->db->table('playa_relationships')
                    ->insert($data);
            }

            return '1';
        }
    }
}
