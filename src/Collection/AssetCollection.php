<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\Asset
 */
class AssetCollection extends Collection
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
}
