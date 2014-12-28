<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixCol;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\MatrixCol
 */
class MatrixColCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a MatrixCol to this collection
     * @param  \rsanchez\Deep\Model\MatrixCol $item
     * @return void
     */
    public function add(MatrixCol $item)
    {
        $this->items[] = $item;
    }
}
