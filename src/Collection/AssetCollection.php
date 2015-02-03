<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Asset;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\Asset
 */
class AssetCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addAsset($item);
    }

    /**
     * Add an Asset to this collection
     * @param  \rsanchez\Deep\Model\Asset $item
     * @return void
     */
    public function addAsset(Asset $item)
    {
        $this->items[] = $item;
    }

    /**
     * Merge am AssetCollection with this collection
     * @param  \rsanchez\Deep\Collection\AssetCollection $assets
     * @return void
     */
    public function addAssets(AssetCollection $assets)
    {
        $this->items += $assets->all();
    }

    /**
     * Add an Asset by ID to this collection
     * @param  int  $assetId
     * @return void
     */
    public function addAssetId($assetId)
    {
        $this->addAsset(Asset::find($assetId));
    }

    /**
     * Add Assets by ID to this collection
     * @param  array $assetIds
     * @return void
     */
    public function addAssetIds(array $assetIds)
    {
        $this->addAssets(Asset::fileId($assetIds)->get());
    }

    /**
     * Get the URL of the first item in the collection
     * @return string
     */
    public function __toString()
    {
        $asset = $this->first();

        return $asset ? $asset->url : '';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        // flatten the array keys
        return array_values(parent::toArray());
    }
}
