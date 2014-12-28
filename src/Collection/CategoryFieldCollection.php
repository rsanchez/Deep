<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\CategoryField;

/**
 * Collection of \rsanchez\Deep\Model\CategoryField
 */
class CategoryFieldCollection extends AbstractFieldCollection
{
    /**
     * Add a CategoryField to this collection
     * @param  \rsanchez\Deep\Model\CategoryField $item
     * @return void
     */
    public function add(CategoryField $item)
    {
        parent::add($item);
    }
}
