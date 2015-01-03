<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\CategoryField;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\CategoryField
 */
class CategoryFieldCollection extends AbstractFieldCollection
{
    /**
     * {@inheritdoc}
     */
    public function addModel(Model $item)
    {
        $this->addCategoryField($item);
    }

    /**
     * Add a CategoryField to this collection
     * @param  \rsanchez\Deep\Model\CategoryField $item
     * @return void
     */
    public function addCategoryField(CategoryField $item)
    {
        $this->addField($item);
    }
}
