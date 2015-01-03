<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\AbstractProperty;
use Illuminate\Database\Eloquent\Collection;

/**
 * Collection of \rsanchez\Deep\Model\AbstractProperty
 */
class PropertyCollection extends Collection
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        $this->add($item);
    }

    /**
     * Add a GridCol to this collection
     * @param  \rsanchez\Deep\Model\AbstractProperty $item
     * @return void
     */
    public function add(AbstractProperty $item)
    {
        $this->items[] = $item;
    }
}
