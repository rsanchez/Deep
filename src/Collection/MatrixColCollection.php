<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixCol;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\MatrixCol
 */
class MatrixColCollection extends PropertyCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addMatrixCol($item);
    }

    /**
     * Add a MatrixCol to this collection
     * @param  \rsanchez\Deep\Model\MatrixCol $item
     * @return void
     */
    public function addMatrixCol(MatrixCol $item)
    {
        $this->addProperty($item);
    }
}
