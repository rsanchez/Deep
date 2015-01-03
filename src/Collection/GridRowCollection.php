<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\GridRow;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\GridRow
 */
class GridRowCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addGridRow($item);
    }

    /**
     * Add a GridRow to this collection
     * @param  \rsanchez\Deep\Model\GridRow $item
     * @return void
     */
    public function addGridRow(GridRow $item)
    {
        $this->items[] = $item;
    }
}
