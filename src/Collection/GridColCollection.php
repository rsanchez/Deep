<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\GridCol;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\GridCol
 */
class GridColCollection extends PropertyCollection
{
    /**
     * Add a GridCol to this collection
     * @param  \rsanchez\Deep\Model\GridCol $item
     * @return void
     */
    public function add(GridCol $item)
    {
        parent::add($item);
    }
}
