<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\ConnectionInterface;
use rsanchez\Deep\Collection\EntryCollection;
use rsanchez\Deep\Collection\AssetCollection;
use rsanchez\Deep\Model\AbstractProperty;
use rsanchez\Deep\Model\AbstractEntity;
use rsanchez\Deep\Model\Asset;
use rsanchez\Deep\Repository\UploadPrefRepositoryInterface;

/**
 * Hydrator for the Assets fieldtype
 */
class AssetsHydrator extends AbstractHydrator
{
    /**
     * @var \rsanchez\Deep\Model\Asset
     */
    protected $model;

    /**
     * Asset selections sorted out by entity (entry or matrix or grid)
     * @var array
     */
    protected $selections = array();

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
     */
    protected $uploadPrefRepository;

    /**
     * {@inheritdoc}
     *
     * @param \Illuminate\Database\ConnectionInterface                $db
     * @param \rsanchez\Deep\Collection\EntryCollection               $collection
     * @param \rsanchez\Deep\Hydrator\HydratorCollection              $hydrators
     * @param string                                                  $fieldtype
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(ConnectionInterface $db, EntryCollection $collection, HydratorCollection $hydrators, $fieldtype, Asset $model, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($db, $collection, $hydrators, $fieldtype);

        $this->model = $model;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $entryIds)
    {
        $assets = $this->model->entryId($entryIds)->orderBy('sort_order')->get();

        foreach ($assets as $asset) {
            if (! $asset->filedir_id || ! $uploadPref = $this->uploadPrefRepository->find($asset->filedir_id)) {
                if (! is_null($asset->source_id) && $asset->source_settings) {
                    $uploadPref = null;
                } else {
                    continue;
                }
            }

            $asset->setUploadPref($uploadPref);

            if ($asset->content_type === 'matrix' || $asset->content_type === 'grid') {
                if (! isset($this->selections[$asset->content_type][$asset->row_id][$asset->col_id])) {
                    $this->selections[$asset->content_type][$asset->row_id][$asset->col_id] = new AssetCollection();
                }

                $this->selections[$asset->content_type][$asset->row_id][$asset->col_id]->push($asset);
            } else {
                if (! isset($this->selections['entry'][$asset->entry_id][$asset->field_id])) {
                    $this->selections['entry'][$asset->entry_id][$asset->field_id] = new AssetCollection();
                }

                $this->selections['entry'][$asset->entry_id][$asset->field_id]->push($asset);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        if (isset($this->selections[$entity->getType()][$entity->getId()][$property->getId()])) {
            $value = $this->selections[$entity->getType()][$entity->getId()][$property->getId()];
        } else {
            $value = new AssetCollection();
        }

        $entity->setAttribute($property->getName(), $value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function dehydrate(AbstractEntity $entity, AbstractProperty $property)
    {
        $assets = $entity->getAttribute($property->getName());

        // drop old relations
        $this->db->table('assets_selections')
            ->where($property->getPrefix().'_id', $property->getId())
            ->where($entity->getPrefix().'_id', $entity->getId())
            ->delete();

        if ($assets) {
            foreach ($assets as $asset) {
                /*
                $this->db->table('assets_selections')
                    ->insert([
                        $property->getPrefix().'_id' => $property->getId(),
                        $entity->getPrefix().'_id' => $entity->getId(),
                        $asset->file_id,
                    ]
                */
            }

            return '1';
        }
    }
}
