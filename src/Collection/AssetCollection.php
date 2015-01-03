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
     * Add a Asset to this collection
     * @param  \rsanchez\Deep\Model\Asset $item
     * @return void
     */
    public function addAsset(Asset $item)
    {
        $this->items[] = $item;
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
