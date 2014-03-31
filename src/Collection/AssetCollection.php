<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Collection\AbstractFilterableCollection;

/**
 * Collection of \rsanchez\Deep\Model\Asset
 */
class AssetCollection extends AbstractFilterableCollection
{
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
