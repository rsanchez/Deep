<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\PropertyInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\PropertyInterface
 */
class PropertyCollection extends AbstractModelCollection
{
    /**
     * {@inheritdoc}
     * @param \rsanchez\Deep\Model\PropertyInterface $item
     */
    public function addModel(Model $item)
    {
        $this->addProperty($item);
    }

    /**
     * Add an PropertyInterface to this collection
     * @param  \rsanchez\Deep\Model\PropertyInterface $item
     * @return void
     */
    public function addProperty(PropertyInterface $item)
    {
        $this->items[] = $item;
    }
}
