<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\MatrixRow;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\MatrixRow
 */
class MatrixRowCollection extends Collection implements FilterableInterface
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
     * Add a MatrixRow to this collection
     * @param  \rsanchez\Deep\Model\MatrixRow $item
     * @return void
     */
    public function add(MatrixRow $item)
    {
        $this->items[] = $item;
    }
}
