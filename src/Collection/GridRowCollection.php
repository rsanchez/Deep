<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\GridRow;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\GridRow
 */
class GridRowCollection extends Collection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a GridRow to this collection
     * @param  \rsanchez\Deep\Model\GridRow $item
     * @return void
     */
    public function add(GridRow $item)
    {
        $this->items[] = $item;
    }
}
