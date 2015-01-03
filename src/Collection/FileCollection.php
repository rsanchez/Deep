<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\File;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\File
 */
class FileCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addFile($item);
    }

    /**
     * Add a File to this collection
     * @param  \rsanchez\Deep\Model\File $item
     * @return void
     */
    public function addFile(File $item)
    {
        $this->items[] = $item;
    }
}
