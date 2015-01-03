<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\GridCol;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\GridCol
 */
class GridColCollection extends PropertyCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addGridCol($item);
    }

    /**
     * Add a GridCol to this collection
     * @param  \rsanchez\Deep\Model\GridCol $item
     * @return void
     */
    public function addGridCol(GridCol $item)
    {
        $this->addProperty($item);
    }
}
