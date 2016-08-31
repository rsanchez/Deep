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
use rsanchez\Deep\Model\UploadPref;
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
    protected $selections = [];

    /**
     * UploadPref model repository
     * @var \rsanchez\Deep\Repository\UploadPrefRepositoryInterface
     */
    protected $uploadPrefRepository;

    /**
     * {@inheritdoc}
     *
     * @param \rsanchez\Deep\Hydrator\HydratorCollection              $hydrators
     * @param string                                                  $fieldtype
     * @param \rsanchez\Deep\Repository\UploadPrefRepositoryInterface $uploadPrefRepository
     */
    public function __construct(HydratorCollection $hydrators, $fieldtype, Asset $model, UploadPrefRepositoryInterface $uploadPrefRepository)
    {
        parent::__construct($hydrators, $fieldtype);

        $this->model = $model;

        $this->uploadPrefRepository = $uploadPrefRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(EntryCollection $collection)
    {
        $assets = $this->model->entryId($collection->getEntryIds())->orderBy('sort_order')->get();

        foreach ($assets as $asset) {
            if (! $asset->filedir_id || ! $uploadPref = $this->uploadPrefRepository->find($asset->filedir_id)) {
                if (! is_null($asset->source_id) && $asset->source_settings) {
                    $uploadPref = new UploadPref([
                        'url' => $asset->source_settings->url_prefix.$asset->source_settings->subfolder,
                    ]);
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
    public function hydrate(AbstractEntity $entity, PropertyInterface $property)
    {
        $entity->addCustomFieldSetter($property->getName(), [$this, 'setter']);

        if (isset($this->selections[$entity->getType()][$entity->getId()][$property->getId()])) {
            return $this->selections[$entity->getType()][$entity->getId()][$property->getId()];
        }

        return new AssetCollection();
    }

    /**
     * Setter callback
     * @param  \rsanchez\Deep\Collection\AssetCollection|array|null $value
     * @return \rsanchez\Deep\Collection\AssetCollection|null
     */
    public function setter($value = null, PropertyInterface $property = null)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof AssetCollection) {
            return $value;
        }

        if (is_array($value)) {
            $assets = new AssetCollection();

            $assets->addAssetIds($value);

            return $assets;
        }

        throw new \InvalidArgumentException('$value must be of type array, null, or \rsanchez\Deep\Collection\AssetCollection.');
    }
}
