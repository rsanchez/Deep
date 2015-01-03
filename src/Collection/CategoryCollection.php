<?php

/**
 * Deep
 *
 * @package      rsanchez\Deep
 * @author       Rob Sanchez <info@robsanchez.com>
 */

namespace rsanchez\Deep\Collection;

use rsanchez\Deep\Model\Category;
use Illuminate\Database\Eloquent\Model;

/**
 * Collection of \rsanchez\Deep\Model\Category
 */
class CategoryCollection extends AbstractModelCollection implements FilterableInterface
{
    use FilterableTrait;

    /**
     * {@inheritdoc}
     */
    public function addModel(Model$item)
    {
        $this->addCategory($item);
    }

    /**
     * Add a Category to this collection
     * @param  \rsanchez\Deep\Model\Category $item
     * @return void
     */
    public function addCategory(Category $item)
    {
        $this->items[] = $item;
    }

    /**
     * Filter by cat_id attribute
     *
     * @param  string                                       $filter pipe-delimited list of cat_ids, optionaly prefixed by not
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function filterByShow($filter)
    {
        return $this->filterByAttributeInString('cat_id', $filter);
    }

    /**
     * Filter by group_id attribute
     *
     * @param  string                                       $filter pipe-delimited list of group_ids, optionaly prefixed by not
     * @return \rsanchez\Deep\Collection\CategoryCollection
     */
    public function filterByShowGroup($filter)
    {
        return $this->filterByAttributeInString('group_id', $filter);
    }
}
