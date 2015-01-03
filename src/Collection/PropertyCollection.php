<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\AbstractProperty;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\AbstractProperty
 */
class PropertyCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     * @param \rsanchez\Deep\Model\AbstractProperty $item
     */
    public function addModel(Model $item)
    {
        $this->addProperty($item);
    }

    /**
     * Add an AbstractProperty to this collection
     * @param  \rsanchez\Deep\Model\AbstractProperty $item
     * @return void
     */
    public function addProperty(AbstractProperty $item)
    {
        $this->items[] = $item;
    }
}
