<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\UploadPref;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\UploadPref
 */
class UploadPrefCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a UploadPref to this collection
     * @param  \rsanchez\Deep\Model\UploadPref $item
     * @return void
     */
    public function add(UploadPref $item)
    {
        $this->items[] = $item;
    }
}
