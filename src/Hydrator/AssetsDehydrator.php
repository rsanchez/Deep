<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\AssetCollection;
use rsanchez\Deep\Model\PropertyInterface;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Dehydrator for the Assets fieldtype
 */
class AssetsDehydrator extends AbstractDehydrator
{
    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, PropertyInterface $property, AbstractEntity $parentEntity = null, PropertyInterface $parentProperty = null)
    {
        $assets = $entity->{$property->getName()};

        // drop old relations
        $query = $this->db->table('assets_selections')
            ->where($property->getPrefix().'_id', $property->getId())
            ->where($entity->getPrefix().'_id', $entity->getId());

        if ($parentEntity && $parentProperty) {
            $query->where($parentProperty->getPrefix().'_id', $parentProperty->getId())
                ->where($parentEntity->getPrefix().'_id', $parentEntity->getId());
        }

        $query->delete();

        $output = [];

        if ($assets) {
            foreach ($assets as $i => $asset) {
                if (! $asset->exists) {
                    $asset->save();
                }

                $data = [
                    $property->getPrefix().'_id' => $property->getId(),
                    $entity->getPrefix().'_id' => $entity->getId(),
                    'file_id' => $asset->file_id,
                    'content_type' => $entity->getType() === 'entry' ? null : $entity->getType(),
                    'sort_order' => $i,
                ];

                if ($parentEntity && $parentProperty) {
                    $data[$parentProperty->getPrefix().'_id'] = $parentProperty->getId();
                    $data[$parentEntity->getPrefix().'_id'] = $parentEntity->getId();
                }

                $this->db->table('assets_selections')
                    ->insert($data);
            }

            // order by file_id
            $assets->slice(0)->sort(function ($a, $b) {
                return $a->file_id > $b->file_id ? 1 : -1;
            })->each(function ($asset) use (&$output) {
                $output[] = $asset->file_name;
            });
        }

        return implode("\n", $output);
    }
}
