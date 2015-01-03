<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixRow;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\MatrixRow
 */
class MatrixRowCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addMatrixRow($item);
    }

    /**
     * Add a MatrixRow to this collection
     * @param  \rsanchez\Deep\Model\MatrixRow $item
     * @return void
     */
    public function addMatrixRow(MatrixRow $item)
    {
        $this->items[] = $item;
    }
}
