<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\File;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\File
 */
class FileCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a File to this collection
     * @param  \rsanchez\Deep\Model\File $item
     * @return void
     */
    public function add(File $item)
    {
        $this->items[] = $item;
    }
}
