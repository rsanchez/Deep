<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\UploadPref;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\UploadPref
 */
class UploadPrefCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addUploadPref($item);
    }

    /**
     * Add a UploadPref to this collection
     * @param  \rsanchez\Deep\Model\UploadPref $item
     * @return void
     */
    public function addUploadPref(UploadPref $item)
    {
        $this->items[] = $item;
    }
}
