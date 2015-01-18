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
 * Dehydrator for the Playa fieldtype
 */
class PlayaDehydrator extends AbstractDehydrator
{
    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null)
    {
        $entries = $entity->{$property->getName()};

        $output = [];

        if ($entries) {
            foreach ($entries as $i => $entry) {
                if (! $entry->exists) {
                    $entry->save();
                }

                $data = [
                    'parent_'.$property->getPrefix().'_id' => $property->getId(),
                    'parent_'.$entity->getPrefix().'_id' => $entity->getId(),
                    'child_entry_id' => $entry->entry_id,
                    'rel_order' => $i,
                ];

                if ($parentEntity && $parentProperty) {
                    $data['parent_'.$parentProperty->getPrefix().'_id'] = $parentProperty->getId();
                    $data['parent_'.$parentEntity->getPrefix().'_id'] = $parentEntity->getId();
                }

                $query = $this->db->table('playa_relationships');

                if ($entry->rel_id) {
                    $query->where('rel_id', $entry->rel_id)
                        ->update($data);
                } else {
                    $query->insert($data);
                }

                $output[] = sprintf('[%s] [%s] %s', $entry->entry_id, $entry->url_title, $entry->title);
            }
        }

        return implode("\n", $output);
    }
}
