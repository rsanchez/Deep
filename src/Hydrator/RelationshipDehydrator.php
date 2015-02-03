<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;

/**
 * Dehydrator for the Relationship fieldtype
 */
class RelationshipDehydrator extends AbstractDehydrator
{
    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null)
    {
        $entries = $entity->{$property->getName()};

        $relIds = [];

        if ($entries) {
            foreach ($entries as $i => $entry) {
                if (! $entry->exists) {
                    $entry->save();
                }

                $data = [
                    'child_id' => $entry->entry_id,
                    'order' => $i + 1,
                ];

                if ($parentEntity && $parentProperty) {
                    $data['parent_id'] = $parentEntity->getId();
                    $data[$parentProperty->getPrefix().'_id'] = $property->getId();//field_id
                    $data[$entity->getType().'_'.$parentProperty->getPrefix().'_id'] = $parentProperty->getId();//grid_field_id
                    $data[$entity->getType().'_'.$property->getPrefix().'_id'] = $property->getId();//grid_col_id
                    $data[$entity->getType().'_'.$entity->getPrefix().'_id'] = $entity->getId();//grid_row_id
                } else {
                    $data['parent_id'] = $entity->getId();
                    $data[$property->getPrefix().'_id'] = $property->getId();
                }

                $query = $this->db->table('relationships');

                if ($entry->relationship_id) {
                    $query->where('relationship_id', $entry->relationship_id)
                        ->update($data);
                } else {
                    $entry->relationship_id = $query->insertGetId($data);
                }

                $relIds[] = $entry->relationship_id;
            }
        }

        $query = $this->db->table('relationships');

        if ($relIds) {
            $query->whereNotIn('relationship_id', $relIds);
        }

        if ($parentEntity && $parentProperty) {
            $query->where('parent_id', $parentEntity->getId())
                ->where($entity->getType().'_'.$parentProperty->getPrefix().'_id', $parentProperty->getId())//grid_field_id
                ->where($entity->getType().'_'.$property->getPrefix().'_id', $property->getId())//grid_col_id
                ->where($entity->getType().'_'.$entity->getPrefix().'_id', $entity->getId());//grid_row_id
        } else {
            $query->where('parent_id', $entity->getId())
                ->where($property->getPrefix().'_id', $property->getId());
        }

        $query->delete();

        return '';
    }
}
